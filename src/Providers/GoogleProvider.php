<?php
declare(strict_types=1);

namespace Marmits\Oauth2Identification\Providers;
use Exception;
use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use League\OAuth2\Client\Provider\GoogleUser;
/**
 *
 */
class GoogleProvider extends AbstractProvider
{
    private  $name = 'google';
    protected  $params;


    /**
     * @param HttpClientInterface $client
     * @param array $googleclient_params
     */
    public function __construct(HttpClientInterface $client, array $googleclient_params)
    {
        parent::__construct($client);
        $this->params = $googleclient_params;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return string
     */
    public function getName(): string{
        return $this->name;
    }

    /**
     * @return Google
     */
    public function getInstance(): Google
    {

        $params = [
            'clientId'     => $this->params['googleclient_params']['client_id'],
            'clientSecret' => $this->params['googleclient_params']['client_secret'],
            'redirectUri'  => $this->params['googleclient_params']['redirect_uris'],
            'scope' => ['https://www.googleapis.com/auth/userinfo.profile']
        ];
        if($this->params['googleclient_params']['google_origins'] !== ''){
            $params['hostedDomain'] = $this->params['googleclient_params']['google_origins'];
        }

        return new Google($params);

    }


    /**
     * @param $datas_access
     * @return array
     * @throws Exception
     */
    public function fetchUser($datas_access): array
    {

        $this->client = $this->client->withOptions([
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $response = $this->client->request(
            'GET',
            'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token='.$datas_access['accesstoken']
        );


        if(array_key_exists('openidinfos',$datas_access)){
            return array_merge($this->getClientHttpReponse($response), $datas_access['openidinfos']);
        }
        return $this->getClientHttpReponse($response);

    }

    /**
     * @param $datas_access
     * @return array
     * @throws Exception
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

            return array_merge($this->getClientHttpReponse($response), $options);

        }
        return [];

    }

}