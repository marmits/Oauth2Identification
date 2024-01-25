<?php
declare(strict_types=1);

namespace Marmits\Oauth2Identification\Controller;

use Exception;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Marmits\Oauth2Identification\Services\UserApi;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;



/**
 *
 */
class IndexController extends AbstractController
{


    protected UserApi $userApi;
    protected RequestStack $requestStack;

    /**
     * @param ContainerInterface $container
     * @param RequestStack $requestStack
     * @param UserApi $userApi
     */
    public function __construct(ContainerInterface $container, RequestStack $requestStack, UserApi $userApi)
    {
        $this->container = $container;
        $this->requestStack = $requestStack;
        $this->userApi = $userApi;
    }

    /**
     * Redirection privat route
     * @Route("/bundle_index", name="bundle_index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->redirectToRoute('bundle_private');
    }



    /**
     * Redirection bundle_index route
     * Reset Session
     * @Route("/logout", name="logout")
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request): Response
    {
        $this->requestStack->getSession()->clear();
        return $this->redirectToRoute('bundle_index');

    }

    /**
     * Rendu des données de base fournies par le provider enregistrées dans la session de l'utlisateur, une fois connecté.
     * MarmitsOauth2Identification/bundle_private.html.twig
     * @Route("/bundle_private", name="bundle_private")
     * @param Request $request
     * @return Response
     */
    public function bundlePrivate(Request $request): Response
    {
        $user = $this->userApi->fetch($request);
        return $this->render('@MarmitsOauth2Identification/privateDefault.html.twig', ['user' => $user]);
    }

    /**
     * Renvoi l'utilisateur autorisé son email et l'access renovoyé stocké dans la session
     * @Route("/bundlesaveaccesstoken", options={"expose"=true}, name="bundlesaveaccesstoken", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAccessToken(Request $request): JsonResponse
    {
        if($this->requestStack->getSession()->has('oauth_user_infos')){
            return new jsonResponse(
                [
                    'code'=> 200, 'message' => 'ok authorisation',
                    'email' => $this->requestStack->getSession()->get('oauth_user_infos')['email'],
                    'api_user_id' => $this->requestStack->getSession()->get('oauth_user_infos')['api_user_id'],
                    'accesstoken' => $this->requestStack->getSession()->get('oauth_user_infos')['accesstoken']
                ], 200);
        }
        return new jsonResponse(['code'=> 401, 'message' => 'Accès interdit'], 401);
    }




}
