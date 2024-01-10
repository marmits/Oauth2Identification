<?php

namespace Marmits\GoogleIdentification\Providers;
use Doctrine\DBAL\Exception;
use League\OAuth2\Client\Provider\Google;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
/**
 *
 */
class GoogleProvider
{
    private string $name = 'google';
    protected array $params;
    private HttpClientInterface $client;

    /**
     * @param array $googleclient_params
     */
    public function __construct( HttpClientInterface $client, array $googleclient_params)
    {
        $this->client = $client;
        $this->params = $googleclient_params;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function getName(): string{
        return $this->name;
    }


    /**
     * @return HttpClientInterface
     */
    public function getClient(): HttpClientInterface
    {
        return $this->client;
    }


    /**
     * @return Google
     */
    public function getInstance(): Google
    {

        return new Google([
            'clientId'     => $this->params['googleclient_params']['client_id'],
            'clientSecret' => $this->params['googleclient_params']['client_secret'],
            'redirectUri'  => $this->params['googleclient_params']['redirect_uris'],
            'hostedDomain' => $this->params['googleclient_params']['google_origins'],
            'scopes' => ['https://mail.google.com', 'https://www.googleapis.com/auth/gmail.readonly']
        ]);

    }


    /**
     * @return array
     */
    public function fetchEmailUser($accestoken): array
    {



        $this->client = $this->client->withOptions([

            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$accestoken
            ]
        ]);




        try {
            $response = $this->client->request(
                'GET',
                'https://www.googleapis.com/gmail/v1/users/me/profile'
            );
        } catch(TransportExceptionInterface $e){

        }





        $statusCode = $response->getStatusCode();


        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        return $content;
    }

}