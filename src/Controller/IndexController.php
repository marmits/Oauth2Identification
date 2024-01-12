<?php

namespace Marmits\GoogleIdentification\Controller;

use Exception;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
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
use Marmits\GoogleIdentification\Providers\GoogleProvider;
use Marmits\GoogleIdentification\Providers\GithubProvider;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\GithubResourceOwner;


/**
 *
 */
class IndexController extends AbstractController
{


    protected GoogleProvider $googleProvider;
    protected GithubProvider $githubProvider;
    protected RequestStack $requestStack;

    /**
     * @param ContainerInterface $container
     * @param RequestStack $requestStack
     * @param GoogleProvider $googleProvider
     * @param GithubProvider $githubProvider
     */
    public function __construct(ContainerInterface $container, RequestStack $requestStack, GoogleProvider $googleProvider, GithubProvider $githubProvider)
    {
        $this->container = $container;
        $this->requestStack = $requestStack;
        $this->googleProvider = $googleProvider;
        $this->githubProvider = $githubProvider;

    }

    /**
     *
     * @Route("/bundle_index", name="bundle_index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {



        return $this->redirectToRoute('bundle_privat');
    }


    /**
     *
     * @Route("/bundle_privat", name="bundle_privat")
     * @param Request $request
     * @return Response
     */
    public function private(Request $request): Response
    {
        $user = [];


        if($this->requestStack->getSession()->has('access')){
            $datas_access = $this->requestStack->getSession()->get('access');
            $user = $this->githubProvider->fetchEmailUser($datas_access['accesstoken']);
        }


        return $this->render('@MarmitsGoogleIdentification/private.html.twig', $user);
    }


    /**
     *
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
        return new jsonResponse(['code'=> 401, 'message' => 'Invalid Access Token'], 500);
    }

    /**
     *
     * @Route("/logout", name="logout")
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request): Response
    {

        if($this->requestStack->getSession()->has('access')){
            $this->requestStack->getSession()->remove('access');
        }
        if($this->requestStack->getSession()->has('oauth2state')){
            $this->requestStack->getSession()->remove('oauth2state');
        }
        $this->requestStack->getSession()->clear();



        return $this->redirectToRoute('bundle_index');

    }



    /**
     *
     * @Route("/getaccesstokengithub", name="getaccesstokengithub")
     *
     * @param Request $request
     * @return JsonResponse
     * @throws IdentityProviderException
     */
    public function getaccesstokenGithub(Request $request): JsonResponse
    {


            if ($request->get('code') === null)
            {

                $authorizationUrl = $this->githubProvider->getInstance()->getAuthorizationUrl();

                // If we don't have an authorization code then get one
                $this->requestStack->getSession()->set('oauth2state', $this->githubProvider->getInstance()->getState());


                header('Location: ' . $authorizationUrl);

                exit;

            } elseif (($request->get('state') === null) || ($this->requestStack->getSession()->has('oauth2state') && $request->get('state') !== $this->requestStack->getSession()->get('oauth2state')))
            {

                // State is invalid, possible CSRF attack in progress
                if ($this->requestStack->getSession()->has('oauth2state')) {
                    $this->requestStack->getSession()->remove('oauth2state');
                }

                if ($this->requestStack->getSession()->has('access')) {
                    $this->requestStack->getSession()->remove('access');
                }

                return new jsonResponse(['message' => 'Invalid state'], 500);


            } else {

                // Try to get an access token (using the authorization code grant)
                $accessToken = $this->githubProvider->getInstance()->getAccessToken('authorization_code', [
                    'code' => $request->get('code'),
                    'provider' => 'github'
                ]);

                // Optional: Now you have a token you can look up a users profile data
                try {

                    // We got an access token, let's now get the owner details

                    $ownerDetails = $this->githubProvider->getInstance()->getResourceOwner($accessToken);
                    if ($ownerDetails instanceof GithubResourceOwner) {
                        $this->requestStack->getSession()->set('access',
                            [
                                'accesstoken' => $accessToken->getToken(),
                                'refreshtoken' => $accessToken->getRefreshToken(),
                                'email' => $ownerDetails->getEmail(),
                                'authorization_code' => $request->get('code'),
                                'client_id' =>  $this->githubProvider->getParams()['githubclient_params']['client_id']
                            ]
                        );

                    }

                    header('Location: ' . 'bundle_privat');
                    exit;


                } catch (IdentityProviderException $e) {
                    // Failed to get user details
                    exit('Something went wrong: ' . $e->getMessage());

                }

            }


    }


    /**
     *
     * @Route("/bundlegetgithubauthentification", options={"expose"=true}, name="getgithubauthentification", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getGithubAuthentification(Request $request): JsonResponse
    {

        if($this->requestStack->getSession()->has('access')){
            $datas_access = $this->requestStack->getSession()->get('access');
            $datas = $this->githubProvider->fetchAuthentification($datas_access['accesstoken']);
            return new jsonResponse(
                [
                    $datas
                ], 200);
        }

        return new jsonResponse(['code'=> 401, 'message' => 'Invalid Access Token'], 500);
    }


    /**
     *
     * @Route("/github_authorize", name="github_authorize")
     * @param Request $request
     * @return Response
     */
    public function githubgetauthorize(Request $request): Response
    {
        $authorizationUrl =  $this->githubProvider->getInstance()->getAuthorizationUrl();
        header('Location: ' . $authorizationUrl);
        exit;
    }

}
