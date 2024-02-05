<?php
declare(strict_types=1);

namespace Marmits\Oauth2Identification\Providers;
use Exception;
use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Provider\AbstractProvider as LeagueProvider;
use Marmits\Oauth2Identification\Dto\AccessInput;
use Marmits\Oauth2Identification\Services\UserApi;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 *
 */
class GoogleProvider extends AbstractProvider implements ProviderInterface
{
    public const PROVIDER_NAME = 'google';
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
     * Demande d'autorisation a google
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
     * @throws Exception
     */
    public function getaccesstoken(Request $request): JsonResponse
    {
        if ($request->get('code') === null)
        {
            $authorizationUrl = $this->getInstance()->getAuthorizationUrl();
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
        else {
            // Try to get an access token (using the authorization code grant)
            $accessToken = $this->getInstance()->getAccessToken('authorization_code', [
                'code' => $request->get('code'),
                'provider' => 'google'
            ]);
            // Optional: Now you have a token you can look up a users profile data
            try {
                // We got an access token, let's now get the owner details
                $ownerDetails = $this->getInstance()->getResourceOwner($accessToken);
                if ($ownerDetails instanceof GoogleUser) {
                    $openidinfos = $this->fetchOpenIdInfos($accessToken);
                    $accessInput = new AccessInput();
                    $accessInput->provider_name = $this->getName();
                    $accessInput->ownerDetails = $openidinfos;
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
     * League\OAuth2\Client\Provider\Google
     * @return LeagueProvider
     */
    public function getInstance(): LeagueProvider
    {
        $params = [
            'clientId'     => $this->getParams()['client_id'],
            'clientSecret' => $this->getParams()['client_secret'],
            'redirectUri'  => $this->getParams()['redirect_uris'],
            'scope' => ['https://www.googleapis.com/auth/userinfo.profile']
        ];
        if($this->getParams()['google_origins'] !== ''){
            $params['hostedDomain'] = $this->getParams()['google_origins'];
        }

        return new Google($params);

    }

    /**
     * @param AccessInput $datas_access
     * @return array
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function fetchUser(AccessInput $datas_access): array
    {
        $this->client = $this->client->withOptions([
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $response = $this->client->request(
            'GET',
            'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token='.$datas_access->accesstoken
        );

        try {
            if(!empty($datas_access->ownerDetails)){
                return $datas_access->ownerDetails;
            }
            return $this->getClientHttpReponse($response);
        } catch (DecodingExceptionInterface|TransportExceptionInterface $e) {
            throw new Exception($e->getMessage());
        }

    }

    /**
     * @param $datas_access
     * @return array
     * @throws Exception
     * @throws TransportExceptionInterface
     */
    public function fetchOpenIdInfos($datas_access): array
    {

        if($datas_access instanceof AccessToken){

            $this->client = $this->client->withOptions([
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.$datas_access->getToken()
                ]
            ]);


            $options = [
                'token' => $datas_access->getToken(),
                'refreshtoken' => $datas_access->getRefreshToken(),
                'ResourceOwnerId' => $datas_access->getResourceOwnerId()
            ];


            $openidInfo = $this->getInstance()->getResourceOwnerDetailsUrl($datas_access);
            $response = $this->client->request(
                'GET',
                $openidInfo
            );
            try {
                return array_merge($this->getClientHttpReponse($response), $options);
            } catch (DecodingExceptionInterface|TransportExceptionInterface $e) {
                throw new Exception($e->getMessage());
            }
        }
        return [];

    }

}