<?php

namespace Marmits\GoogleIdentification\Services;
use League\OAuth2\Client\Provider\Github;
use Symfony\Contracts\HttpClient\HttpClientInterface;


/**
 *
 */
class GithubProvider
{

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
    public function fetchGitHubInformation(): array
    {
        $response = $this->client->request(
            'GET',
            'https://api.github.com/repos/symfony/symfony-docs'
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
    public function fetchGitHubEmailUser($accestoken): array
    {

        $this->client = $this->client->withOptions([

            'headers' => [
                'Content-Type' => 'application/vnd.github+json',
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


}