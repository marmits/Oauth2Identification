<?php

namespace Marmits\GoogleIdentification\Controller;

use Exception;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Marmits\GoogleIdentification\Services\GoogleProvider;
use Marmits\GoogleIdentification\Services\GithubProvider;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\GithubResourceOwner;


/**
 *
 */
class IndexController extends AbstractController
{

    protected GoogleProvider $googleProvider;
    protected GithubProvider $githubProvider;
    protected SessionInterface $session;

    /**
     * @param ContainerInterface $container
     * @param SessionInterface $session
     * @param GoogleProvider $googleProvider
     * @param GithubProvider $githubProvider
     */
    public function __construct(ContainerInterface $container, SessionInterface $session, GoogleProvider $googleProvider, GithubProvider $githubProvider)
    {
        $this->container = $container;
        $this->session = $session;
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

        return $this->render('@MarmitsGoogleIdentification/default.html.twig', [
            'googleProvider' => $this->googleProvider->getParams(),
            'githubProvider' => $this->githubProvider->getParams()
        ]);
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
        if($this->session->has('access')){
            $datas_access = $this->session->get('access');
            $user = $this->githubProvider->fetchGitHubEmailUser($datas_access['accesstoken']);
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

        if($this->session->has('access')){
            return new jsonResponse(['code'=> 200, 'message' => 'ok authorisation', 'email' => $this->session->get('access')['email']], 200);
        }
        return new jsonResponse(['code'=> 401, 'message' => 'Invalid Access Token'], 500);
    }

    /**
     *
     * @Route("/getaccesstokengoogle", name="getaccesstokengoogle")
     *
     * @param Request $request
     * @return JsonResponse
     * @throws IdentityProviderException
     */
    public function getaccesstokenGoogle(Request $request): JsonResponse
    {
        $code = null;
        $error = null;
        $state = null;
        $id_token = -1;
        if ($request->get('code')) {
            $code = $request->get('code');
        }
        if ($request->get('error')) {
            $error = $request->get('error');
        }
        if ($request->get('state')) {
            $state = $request->get('state');
        }

        $authorizationUrl =  $this->googleProvider->getInstance()->getAuthorizationUrl();

        if ($error !== null) {

            // Got an error, probably user denied access
            exit('Got error: ' . htmlspecialchars( $error['error'], ENT_QUOTES, 'UTF-8'));

        } elseif ($code !== null) {

            // If we don't have an authorization code then get one
            $this->session->set('oauth2state', $this->googleProvider->getInstance()->getState());
            header('Location: ' . $authorizationUrl);
            exit;

        } elseif (($state === null) || ($this->session->has('oauth2state') && $state !== $this->session->get('oauth2state'))) {

            // State is invalid, possible CSRF attack in progress
            if ( $this->session->has('oauth2state')) {
                $this->session->remove('oauth2state');
            }

            return new jsonResponse(['message' => 'Invalid state'], 500);


        } else {

            // Try to get an access token (using the authorization code grant)
            $accessToken = $this->googleProvider->getInstance()->getAccessToken('authorization_code', [
                'code' => $code,
                'provider' => 'github'
            ]);

            // Optional: Now you have a token you can look up a users profile data
            try {
                $res = [];
                // We got an access token, let's now get the owner details
                $ownerDetails = $this->googleProvider->getInstance()->getResourceOwner($accessToken);
                if($ownerDetails instanceof GoogleUser){
                    // Use these details to create a new profile
                    if(isset($accessToken->getValues()['id_token'])){
                        $id_token = $accessToken->getValues()['id_token'];
                    }

                    $res = [
                        'accesstoken' => $accessToken->getToken(),
                        'refreshtoken' => $accessToken->getRefreshToken(),
                        'expiredin' => $accessToken->getExpires(),
                        'expired' => $accessToken->hasExpired() ,
                        'id_token'=> $id_token,
                        'firstName' => $ownerDetails->getFirstName()
                    ];
                }

                return new jsonResponse($res, 200);

            } catch (IdentityProviderException $e) {

                // Failed to get user details
                exit('Something went wrong: ' . $e->getMessage());

            }
        }
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


        if ($request->get('code') === null) {

            $authorizationUrl =  $this->githubProvider->getInstance()->getAuthorizationUrl();

            // If we don't have an authorization code then get one
            $this->session->set('oauth2state', $this->githubProvider->getInstance()->getState());


            header('Location: ' . $authorizationUrl);

            exit;

        } elseif (($request->get('state') === null) || ($this->session->has('oauth2state') && $request->get('state') !== $this->session->get('oauth2state'))) {

            // State is invalid, possible CSRF attack in progress
            if ( $this->session->has('oauth2state')) {
                $this->session->remove('oauth2state');
            }

            if ( $this->session->has('access')) {
                $this->session->remove('access');
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
                if($ownerDetails instanceof GithubResourceOwner){
                    $this->session->set('access',
                        [
                            'accesstoken' => $accessToken->getToken(),
                            'refreshtoken' => $accessToken->getRefreshToken(),
                            'email' => $ownerDetails->getEmail()
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
