<?php
declare(strict_types=1);
namespace Marmits\Oauth2Identification\Providers;

use Exception;
use Marmits\Oauth2Identification\Services\UserApi;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Component\HttpFoundation\Response;


/**
 *
 */
class AccessTokenProcess
{
    private RequestStack $requestStack;
    private GoogleProvider $googleProvider;
    private GithubProvider $githubProvider;
    private UserApi $userApi;

    /**
     * @param RequestStack $requestStack
     * @param GoogleProvider $googleProvider
     * @param GithubProvider $githubProvider
     * @param UserApi $userApi
     */
    public function __construct(
        RequestStack $requestStack,
        GoogleProvider $googleProvider,
        GithubProvider $githubProvider,
        UserApi $userApi
    )
    {
        $this->requestStack = $requestStack;
        $this->googleProvider = $googleProvider;
        $this->githubProvider = $githubProvider;
        $this->userApi = $userApi;
    }

    /**
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
     * @param Request $request
     * @return JsonResponse
     */
    public function getaccesstokenGithub(Request $request): JsonResponse
    {
        $this->userApi->setProviderName($this->githubProvider->getName());
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
        elseif (($request->get('state') === null)
            || ($this->requestStack->getSession()->has('oauth2state') && $request->get('state') !== $this->requestStack->getSession()->get('oauth2state')))
        {
            // State is invalid, possible CSRF attack in progress
            $this->userApi->killSession();
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
                        'api_user_id' => $ownerDetails->getId()
                    ];
                    $this->userApi->setOauthUserIdentifiants($access);
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
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function getaccesstokenGoogle(Request $request): JsonResponse
    {
        $this->userApi->setProviderName($this->googleProvider->getName());
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
            $this->userApi->killSession();
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
                        'ownerDetails' => $ownerDetails->toArray(),
                        'accesstoken' => $accessToken->getToken(),
                        'refreshtoken' => $accessToken->getRefreshToken(),
                        'email' => $ownerDetails->getEmail(),
                        'api_user_id' => $ownerDetails->getId()
                    ];
                    $this->userApi->setOauthUserIdentifiants($access);
                }
                header('Location: ' . 'privat');
                exit;
            } catch (IdentityProviderException $e) {
                // Failed to get user details
                exit('Something went wrong: ' . $e->getMessage());
            }
        }
    }
}