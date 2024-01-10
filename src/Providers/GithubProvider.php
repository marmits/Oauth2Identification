<?php

namespace Marmits\GoogleIdentification\Providers;
use League\OAuth2\Client\Provider\Github;
use Symfony\Contracts\HttpClient\HttpClientInterface;


/**
 *
 */
class GithubProvider
{

    private string $name = 'github';
    protected array $params;
    private HttpClientInterface $client;


    /**
     * @param array $githubclient_params
     */
    public function __construct( HttpClientInterface $client, array $githubclient_params)
    {
        $this->client = $client;
        $this->params = $githubclient_params;
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
     * @return Github
     */
    public function getInstance(): Github
    {

        return new Github([
            'clientId'          => $this->params['githubclient_params']['client_id'],
            'clientSecret'      => $this->params['githubclient_params']['client_secret'],
            'redirectUri'       => $this->params['githubclient_params']['redirect_uris'],
        ]);

    }



    /**
     * @return array
     */
    public function fetchEmailUser($accestoken): array
    {

        $this->client = $this->client->withOptions([

            'headers' => [
                'Accept' => 'application/vnd.github+json',
                'Authorization' => 'Bearer '.$accestoken
            ]
        ]);

        $response = $this->client->request(
            'GET',
            'https://api.github.com/user'
        );



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

    /**
     * @return array
     */
    public function fetchAuthentification($accestoken): array
    {

        $this->client = $this->client->withOptions([

            'headers' => [
                'Accept' => 'application/vnd.github+json',
                'Authorization' => 'Bearer '.$accestoken
            ]
        ]);

        $response = $this->client->request(
            'POST',
            'https://api.github.com/applications/'.$this->params['githubclient_params']['client_id'].'/token',
            [
                'body' => ['access_token' => $accestoken]
            ]
        );



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