<?php
declare(strict_types=1);

namespace Marmits\Oauth2Identification\Providers;
use Exception;
use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 *
 */
class GoogleProvider extends AbstractProvider
{
    public const PROVIDER_NAME = 'google';


    /**
     * @param HttpClientInterface $client
     * @param array $params
     */
    public function __construct(
        HttpClientInterface $client,
        array $params
    )
    {
        parent::__construct($client);
        $this->setName(self::PROVIDER_NAME);
        $this->setParams($params['params']);
    }



    /**
     * @return Google
     */
    public function getInstance(): Google
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