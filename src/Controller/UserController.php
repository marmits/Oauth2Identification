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


class UserController  extends AbstractController
{

    protected RequestStack $requestStack;
    protected EntityManagerInterface $em;
    protected DatasRepository $repository;
    protected Access $access;

    /**
     * @param ContainerInterface $container
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $em
     * @param DatasRepository $repository
     * @param Access $access

     */
    public function __construct(ContainerInterface $container, RequestStack $requestStack, EntityManagerInterface $em, DatasRepository $repository, Access $access)
    {
        $this->container = $container;
        $this->requestStack = $requestStack;
        $this->em = $em;
        $this->repository = $repository;
        $this->access = $access;

    }

    /**
     *
     * @Route("/isvaliduser", name="isvaliduser",options={"expose"=true}, methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getIsValidUSer(Request $request): JsonResponse
    {

        $datas = $this->repository->findAll();
        $count = count($datas);
        if($count > 0) {
            if($this->requestStack->getSession()->has('access')){
                $email_connect = $this->requestStack->getSession()->get('access')["email"];
                $user =  $this->repository->findOneBy(['email' => $email_connect]);
                if($user instanceof Datas) {
                    $contenu = $user->getContenu();
                    return new jsonResponse(['code' => 200, 'message' => 'Authorized User'], 200);
                }
                return new jsonResponse(['code' => 403, 'message' => 'Unauthorized User'], 200);
            }
        }
        return new jsonResponse(['code'=> 404, 'message' => 'Aucune données'], 200);

    }

    /**
     *
     * @Route("/setidentifiantappli", name="setidentifiantappli",options={"expose"=true}, methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function setIdentifiantAppli(Request $request): JsonResponse
    {


        $result["error"] =  true;
        $result["message"] = "bad identifiant";
        $result["identifiant"] = null;
        if($this->requestStack->getSession()->has('access')) {
            $this->requestStack->getSession()->set('identifiant', $this->access->getIdentifiantParam());
            $result["identifiant"] = $this->requestStack->getSession()->get('identifiant');
            $result["error"] = false;
            $result["message"] = "Identifiant correct";
        }

        return new jsonResponse($result, 200);

    }

    /**
     *
     * @Route("/checkprivateaccess", name="checkprivateaccess",options={"expose"=true}, methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkPrivateAccess(Request $request): JsonResponse
    {
        $password = "";

        $result["error"] =  true;
        $result["message"] = "empty password";

        if($request->request->has("password") && $request->request->has("identifiant") ) {
            $identifiant = $request->request->get("identifiant");
            $password = $request->request->get("password");

            $this->access->setIdentifiant($identifiant);
            $this->access->setPassword($password);
            if($this->access->checkCrediental()){
                $result["error"] =  false;
                $result["message"] = "Successfull Login";

            } else {
                $result["error"] =  true;
                $result["message"] = "Bad Login";
            }

            error_log( print_r($_ENV, TRUE) );
        }

        return new jsonResponse($result, 200);

    }

}