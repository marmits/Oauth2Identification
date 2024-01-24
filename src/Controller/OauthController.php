<?php
declare(strict_types=1);
namespace Marmits\GoogleIdentification\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use Marmits\GoogleIdentification\Providers\GithubProvider;
use Marmits\GoogleIdentification\Providers\GoogleProvider;


/**
 * Processus de demande d'authorisation et acces obtenu
 * protocol Oauth2 Goole et Github
 */

class OauthController extends AbstractController
{
    private RequestStack $requestStack;
    private GoogleProvider $googleProvider;
    private GithubProvider $githubProvider;

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

    /*
     * ####################### requete provider #####################
     */
    /**
     * Demande d'autorisation a github
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

    /**
     * Processus Authorization Code Grant
     * @Route("/getaccesstokengithub", name="getaccesstokengithub")
     *
     * @param Request $request
     * @return JsonResponse
     * @throws IdentityProviderException
     */
    public function getaccesstokenGithub(Request $request): JsonResponse
    {
        $this->requestStack->getSession()->set('provider_name', $this->githubProvider->getName());
        if ($request->get('code') === null)
        {

            $options = [
                'state' => 'OPTIONAL_CUSTOM_CONFIGURED_STATE',
                'scope' => ['user','user:email']
            ];
            $authorizationUrl = $this->githubProvider->getInstance()->getAuthorizationUrl($options);

            // If we don't have an authorization code then get one
            $this->requestStack->getSession()->set('oauth2state', $this->githubProvider->getInstance()->getState());
            header('Location: ' . $authorizationUrl);

            exit;

        }
        elseif (($request->get('state') === null) || ($this->requestStack->getSession()->has('oauth2state') && $request->get('state') !== $this->requestStack->getSession()->get('oauth2state')))
        {

            // State is invalid, possible CSRF attack in progress
            if ($this->requestStack->getSession()->has('oauth2state')) {
                $this->requestStack->getSession()->remove('oauth2state');
            }

            if ($this->requestStack->getSession()->has('access')) {
                $this->requestStack->getSession()->remove('access');
            }

            return new jsonResponse(['message' => 'Invalid state'], 500);

        }
        else
        {

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
                    $access = [
                        'ownerDetails' => $ownerDetails,
                        'accesstoken' => $accessToken->getToken(),
                        'refreshtoken' => $accessToken->getRefreshToken(),
                        'email' => $ownerDetails->getEmail(),
                        'api_user_id' => $ownerDetails->getId(),
                        'authorization_code' => $request->get('code'),
                        'client_id' =>  $this->githubProvider->getParams()['githubclient_params']['client_id']
                    ];
                    $this->requestStack->getSession()->set('access',$access);
                }

                header('Location: ' . 'privat');
                exit;


            } catch (IdentityProviderException $e) {
                // Failed to get user details
                exit('Something went wrong: ' . $e->getMessage());
            }

        }
    }

    /**
     * Demande d'autorisation a google
     * @Route("/google_authorize", name="google_authorize")
     * @param Request $request
     * @return Response
     */
    public function googlegetauthorize(Request $request): Response
    {
        $authorizationUrl =  $this->googleProvider->getInstance()->getAuthorizationUrl();
        header('Location: ' . $authorizationUrl);
        exit;
    }

    /**
     * Processus Authorization Code Grant
     * @Route("/getaccesstokengoogle", name="getaccesstokengoogle")
     *
     * @param Request $request
     * @return JsonResponse
     * @throws IdentityProviderException
     */
    public function getaccesstokenGoogle(Request $request): JsonResponse
    {
        $this->requestStack->getSession()->set('provider_name', $this->googleProvider->getName());
        if ($request->get('code') === null)
        {
            $authorizationUrl = $this->googleProvider->getInstance()->getAuthorizationUrl();

            // If we don't have an authorization code then get one
            $this->requestStack->getSession()->set('oauth2state', $this->googleProvider->getInstance()->getState());
            header('Location: ' . $authorizationUrl);

            exit;

        }
        elseif (($request->get('state') === null) || ($this->requestStack->getSession()->has('oauth2state') && $request->get('state') !== $this->requestStack->getSession()->get('oauth2state')))
        {

            // State is invalid, possible CSRF attack in progress
            if ($this->requestStack->getSession()->has('oauth2state')) {
                $this->requestStack->getSession()->remove('oauth2state');
            }

            if ($this->requestStack->getSession()->has('access')) {
                $this->requestStack->getSession()->remove('access');
            }

            return new jsonResponse(['message' => 'Invalid state'], 500);

        }
        else {

            // Try to get an access token (using the authorization code grant)
            $accessToken = $this->googleProvider->getInstance()->getAccessToken('authorization_code', [
                'code' => $request->get('code'),
                'provider' => 'google'
            ]);

            // Optional: Now you have a token you can look up a users profile data
            try {

                // We got an access token, let's now get the owner details

                $ownerDetails = $this->googleProvider->getInstance()->getResourceOwner($accessToken);

                if ($ownerDetails instanceof GoogleUser) {

                    $openidinfos = $this->googleProvider->fetchOpenIdInfos($accessToken);

                    $access =  [
                        'openidinfos' => $openidinfos,
                        'ownerDetails' => $ownerDetails,
                        'accesstoken' => $accessToken->getToken(),
                        'refreshtoken' => $accessToken->getRefreshToken(),
                        'email' => $ownerDetails->getEmail(),
                        'api_user_id' => $ownerDetails->getId(),
                        'authorization_code' => $request->get('code'),
                        'client_id' =>  $this->googleProvider->getParams()['googleclient_params']['client_id']
                    ];

                    $this->requestStack->getSession()->set('access',$access);
                }

                
                header('Location: ' . 'privat');
                exit;


            } catch (IdentityProviderException $e) {
                // Failed to get user details
                exit('Something went wrong: ' . $e->getMessage());
            }

        }
    }
    /*
     * #################################################################
     */

    /**
     * Pour TEST
     * @Route("/bundlegetgithubauthentification", name="getgithubauthentification", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function getGithubAuthentification(Request $request): JsonResponse
    {

        if($this->requestStack->getSession()->has('access')){
            $datas_access = $this->requestStack->getSession()->get('access');
            $datas = $this->githubProvider->fetchAuthentification($datas_access);
            return new jsonResponse(
                [
                    $datas
                ], 200);
        }

        return new jsonResponse(['code'=> 401, 'message' => 'Invalid Access Token'], 401);
    }

}