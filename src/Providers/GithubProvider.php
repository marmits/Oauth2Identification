<?php
declare(strict_types=1);

namespace Marmits\Oauth2Identification\Providers;
use Exception;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\Github;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\AbstractProvider as LeagueProvider;
use Marmits\Oauth2Identification\Dto\AccessInput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Marmits\Oauth2Identification\Services\UserApi;


/**
 *
 */
class GithubProvider extends AbstractProvider implements ProviderInterface
{

    public const PROVIDER_NAME = 'github';
    private RequestStack $requestStack;
    private UserApi $userApi;

    /**
     * @param RequestStack $requestStack
     * @param UserApi $userApi
     * @param HttpClientInterface $client
     * @param array $params
     */
    public function __construct(
        RequestStack $requestStack,
        UserApi $userApi,
        HttpClientInterface $client, 
        array $params
    )
    {
        parent::__construct($client);
        $this->requestStack = $requestStack;
        $this->userApi = $userApi;
        $this->setName(self::PROVIDER_NAME);
        $this->setParams($params['params']);

    }

    public function supports(string $type): bool
    {
        return self::PROVIDER_NAME === $type;
    }

    public function build(): AbstractProvider
    {
        return $this;
    }

    /**
     * @return Response
     */
    public function getauthorize(): Response
    {
        $authorizationUrl =  $this->getInstance()->getAuthorizationUrl();
        header('Location: ' . $authorizationUrl);
        exit;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws IdentityProviderException
     */
    public function getaccesstoken(Request $request): JsonResponse
    {
        if ($request->get('code') === null)
        {
            $options = [
                'state' => 'OPTIONAL_CUSTOM_CONFIGURED_STATE',
                'scope' => ['user','user:email']
            ];
            $authorizationUrl = $this->getInstance()->getAuthorizationUrl($options);
            // If we don't have an authorization code then get one
            $this->requestStack->getSession()->set('oauth2state', $this->getInstance()->getState());
            header('Location: ' . $authorizationUrl);
            exit;

        }
        elseif (($request->get('state') === null)
            || (
                $this->requestStack->getSession()->has('oauth2state') && $request->get('state') !== $this->requestStack->getSession()->get('oauth2state')
            ))
        {
            // State is invalid, possible CSRF attack in progress
            $this->userApi->killSession();
            return new jsonResponse(['message' => 'Invalid state'], 500);
        }
        else
        {
            // Try to get an access token (using the authorization code grant)
            $accessToken = $this->getInstance()->getAccessToken('authorization_code', [
                'code' => $request->get('code'),
                'provider' => 'github'
            ]);
            // Optional: Now you have a token you can look up a users profile data
            try {
                // We got an access token, let's now get the owner details
                $ownerDetails = $this->getInstance()->getResourceOwner($accessToken);
                if ($ownerDetails instanceof GithubResourceOwner) {
                    $accessInput = new AccessInput();
                    $accessInput->provider_name = $this->getName();
                    $accessInput->ownerDetails = (array)$ownerDetails;
                    $accessInput->accesstoken = $accessToken->getToken();
                    $accessInput->refreshtoken = $accessToken->getRefreshToken();
                    $accessInput->email = $ownerDetails->getEmail();
                    $accessInput->api_user_id = strval($ownerDetails->getId());
                    $this->userApi->setOauthUser($accessInput);
                }
                header('Location: ' . 'privat');
                exit;
            } catch (Exception|TransportExceptionInterface $e) {
                // Failed to get user details
                exit('Something went wrong: ' . $e->getMessage());
            }
        }
    }

    /**
     * League\OAuth2\Client\Provider\Github
     * @return LeagueProvider
     */
    public function getInstance(): LeagueProvider
    {

        return new Github([
            'clientId'          => $this->getParams()['client_id'],
            'clientSecret'      => $this->getParams()['client_secret'],
            'redirectUri'       => $this->getParams()['redirect_uris'],
        ]);

    }

    /**
     * @param AccessInput $datas_access
     * @return array
     * @throws Exception|TransportExceptionInterface
     */
    public function fetchUser(AccessInput $datas_access): array
    {
        $this->client = $this->client->withOptions([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/vnd.github+json',
                'Authorization' => 'Bearer '.$datas_access->accesstoken
            ]
        ]);

        $response = $this->client->request(
            'GET',
            'https://api.github.com/user'
        );

        try {
            return $this->formatOutPout($this->getClientHttpReponse($response));
        } catch (DecodingExceptionInterface|TransportExceptionInterface $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * TEST
     * @param $accestoken
     * @return array
     * @throws Exception|TransportExceptionInterface
     */
    public function fetchAuthentification($accestoken): array
    {

        $this->client = $this->client->withOptions([

            'headers' => [
                'Accept' => 'application/vnd.github+json',
                'Authorization' => 'Bearer '.$accestoken['accesstoken'],
                'X-GitHub-Api-Version' => '2022-11-28'
            ]
        ]);

        $response = $this->client->request(
            'GET',
            'https://api.github.com/users/marmits/installation',// doit être dans le scope de l'application
        );

        try {
            return $this->getClientHttpReponse($response);
        } catch (DecodingExceptionInterface|TransportExceptionInterface $e) {
            throw new Exception($e->getMessage());
        }

    }

    /**
     * @return array
     * @throws Exception|TransportExceptionInterface
     */
    public function fetchGitHubInformation(): array
    {
        $response = $this->client->request(
            'GET',
            'https://api.github.com/repos/symfony/symfony-docs'
        );

        try {
            return $this->getClientHttpReponse($response);
        } catch (DecodingExceptionInterface|TransportExceptionInterface $e) {
            throw new Exception($e->getMessage());
        }
    }

}