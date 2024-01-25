<?php
declare(strict_types=1);

namespace Marmits\Oauth2Identification\Providers;
use Exception;
use League\OAuth2\Client\Provider\Github;
use Symfony\Component\HttpClient\Response\AsyncResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
/**
 *
 */
class GithubProvider extends AbstractProvider
{

    private $name = 'github';
    protected $params;


    /**
     * @param HttpClientInterface $client
     * @param array $githubclient_params
     */
    public function __construct(HttpClientInterface $client, array $githubclient_params)
    {
        parent::__construct($client);
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
     * @param $datas_access
     * @return array
     * @throws Exception
     */
    public function fetchUser($datas_access): array
    {

        $this->client = $this->client->withOptions([
            'headers' => [
                'Accept' => 'application/vnd.github+json',
                'Authorization' => 'Bearer '.$datas_access['accesstoken']
            ]
        ]);

        $response = $this->client->request(
            'GET',
            'https://api.github.com/user'
        );

        return $this->formatOutPout($this->getClientHttpReponse($response));
    }

    /**
     * TEST
     * @param $accestoken
     * @return array
     * @throws Exception
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

        return $this->getClientHttpReponse($response);

    }

    /**
     * @return array
     * @throws Exception
     */
    public function fetchGitHubInformation(): array
    {
        $response = $this->client->request(
            'GET',
            'https://api.github.com/repos/symfony/symfony-docs'
        );

        return $this->getClientHttpReponse($response);
    }


}