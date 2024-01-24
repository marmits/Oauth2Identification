<?php

namespace Marmits\GoogleIdentification\Controller;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use Marmits\GoogleIdentification\Entity\Datas;
use Marmits\GoogleIdentification\Repository\DatasRepository;
use Marmits\GoogleIdentification\Services\Access;


/**
 * PARTIE APPLICATION LOCALE
 * Gère les routes exposées pour JS
 */

class UserController extends AbstractController
{

    protected RequestStack $requestStack;
    protected EntityManagerInterface $em;
    protected DatasRepository $DatasRepository;
    protected Access $access;

    /**
     * @param ContainerInterface $container
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $em
     * @param DatasRepository $DatasRepository
     * @param Access $access


     */
    public function __construct(ContainerInterface $container, RequestStack $requestStack, EntityManagerInterface $em, DatasRepository $DatasRepository, Access $access)
    {
        $this->container = $container;
        $this->requestStack = $requestStack;
        $this->em = $em;
        $this->DatasRepository = $DatasRepository;
        $this->access = $access;


    }

    /**
     * Renvoi l'utilisateur autorisé son email et l'access renovoyé stocké dans la session
     * @Route("/bundlesaveaccesstoken", options={"expose"=true}, name="bundlesaveaccesstoken", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAccessToken(Request $request): JsonResponse
    {
        if($this->requestStack->getSession()->has('access')){
            return new jsonResponse(
                [
                    'code'=> 200, 'message' => 'ok authorisation',
                    'email' => $this->requestStack->getSession()->get('access')['email'],
                    'accesstoken' => $this->requestStack->getSession()->get('access')['accesstoken']
                ], 200);
        }
        return new jsonResponse(['code'=> 401, 'message' => 'Accès interdit'], 401);
    }

    /**
     * Verifie si l'utilisateur existe dans la BDD
     * @Route("/isvaliduser", name="isvaliduser",options={"expose"=true}, methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getIsValidUSer(Request $request): JsonResponse
    {

        if ($this->getDatasUser() !== null) {
            return new jsonResponse(['code' => 200, 'message' => 'Authorized User'], 200);
        }

        return new jsonResponse(['code'=> 404, 'message' => 'Unauthorized'], 401);

    }

    /**
     * Renvoi est stock l'identifiant de l'appplication local au moment de la connection
     * @Route("/setidentifiantappli", name="setidentifiantappli",options={"expose"=true}, methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function setIdentifiantAppli(Request $request): JsonResponse
    {

        $result['error'] =  true;
        $result['message'] = "bad identifiant";
        $result['identifiant'] = null;
        $code_error = 401;
        if ($this->getDatasUser() !== null) {
            $this->requestStack->getSession()->set('identifiant', $this->access->getIdentifiantParam());
            $result['identifiant'] = $this->requestStack->getSession()->get('identifiant');
            $result['error'] = false;
            $result['message'] = "Identifiant correct";
            $code_error = 200;
        }

        return new jsonResponse($result, $code_error);

    }

    /**
     * Verifie si les crédentials sont corrects et les stocks en sessions
     * @Route("/checkprivateaccess", name="checkprivateaccess",options={"expose"=true}, methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function checkPrivateAccess(Request $request): JsonResponse
    {

        $result['error'] =  true;
        $result['message'] = "Not identified user";
        $codeError = 401;
        if ($this->getDatasUser() !== null) {
            if ($request->request->has("password") && $request->request->has("identifiant")) {
                $identifiant = $request->request->get("identifiant");
                $password = $request->request->get("password");
                $this->access->setIdentifiant($identifiant);
                $this->access->setPassword($this->access->geFirstPartPassword().$password);
                if ($this->access->checkCrediental()) {
                    $result['error'] = false;
                    $result['message'] = "Successfull Login";
                    $codeError = 200;
                    $this->requestStack->getSession()->set('privateaccess', $identifiant.$this->access->getRealPassword());
                } else {
                    $result['error'] = true;
                    $result['message'] = "Bad Login";
                    $codeError = 403;
                }
            } else {
                $result['message'] = "empty password";
                $codeError = 200;
            }
        }

        return new jsonResponse($result, $codeError);

    }

    /**
     * Si l'utlisateur est authorisé Retourne le champs contenu de la table data en tenant compte du parametre crypté ou non
     * @Route("/privatedatasaccess", name="privatedatasaccess",options={"expose"=true}, methods={"GET"})
     * @return JsonResponse
     */
    public function privateDatasAccess(): JsonResponse
    {

        $content = $this->getPrivateDatas();

        $datasUser = '';
        if($content['error'] === false){
            if($this->access->isParamCrypted() === true) {
                $datasUser = $this->access->getDatasCrypted($this->getDatasUser()->getContenu());
            } else {
                $datasUser = $this->getDatasUser()->getContenu();
            }
        } else {
            return new jsonResponse($content['message'], $content['errorCode']);
        }

        return new jsonResponse($datasUser, $content['errorCode']);
    }

    /**
     * Recupere le contenu de la table en fonction de l'email de l'utilisateur
     * @return Datas|null
     */
    private function getDatasUser(): ?Datas
    {
        $datas = $this->DatasRepository->findAll();
        $count = count($datas);
        if($count > 0) {
            if($this->requestStack->getSession()->has('access')){
                $email_connect = $this->requestStack->getSession()->get('access')['email'];
                $api_user_id = $this->requestStack->getSession()->get('access')['api_user_id'];
                $user =  $this->DatasRepository->findOneBy(['email' => $email_connect, 'idApi' => $api_user_id, 'activate' => 1]);
                if($user instanceof Datas) {
                    return $user;
                }
            }
        }
        return null;
    }

    /**
     * Verifie que les credentials stockés en session sont authorisés (passwordHasher verify) en comparant avec le hash
     * @return array
     */
    private function getPrivateDatas(): array
    {

        $result['error'] =  true;
        $result['message'] = 'User Error identification';
        $result['errorCode'] = 401;

        if($this->requestStack->getSession()->has('privateaccess')) {
            //verifier que la connection est correct
            if (!$this->access->VerifIdentifiantPasswordHash($this->requestStack->getSession()->get('privateaccess'))) {
                $result['message'] = 'Credential non valid';
            } //vérifier que l'utilisateur est bien valide et connecté
            elseif ($this->getDatasUser() === null) {
                $result['message'] = 'Utilisateur non valide';
            } else {
                $result['errorCode'] = 200;
                $result['message'] = 'Private datas authorize Yes';
                $result['error'] = false;
            }
        }


        return $result;

    }

}