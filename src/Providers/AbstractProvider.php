<?php
declare(strict_types=1);

namespace Marmits\Oauth2Identification\Providers;

use Exception;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 *
 */
abstract class AbstractProvider
{
    private string $name = '';
    private array $params;
    protected HttpClientInterface $client;

    /**
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param $datas_access
     * @return array
     */
    abstract public function fetchUser($datas_access): array;


    /**
     * @param string $name
     * @return AbstractProvider
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
    /**
     * @return string
     */
    public function getName(): string{
        return $this->name;
    }

    /**
     * @param array $params
     * @return AbstractProvider
     */
    public function setParams(array $params): self
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }


    /**
     * @param ResponseInterface $response
     * @return array
     * @throws Exception
     * @throws TransportExceptionInterface|DecodingExceptionInterface
     */
    public function getClientHttpReponse(ResponseInterface $response): array{
        $statusCode = $response->getStatusCode();

        $infos = [
            $response->getInfo()['http_code'],
            $response->getInfo()['http_method'],
            $response->getInfo()['url'],
        ];

        $content = json_encode($infos,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

        if ($statusCode >= 400 && $statusCode <= 499) {
            throw new HttpException($statusCode, $content, null, [], $statusCode);
        }
        if ($statusCode >= 500 && $statusCode <= 599) {
            throw new Exception($content, $statusCode);
        }

        if($response->getContent() !== null){
            return $response->toArray();
        }

        return [];

    }

    /**
     * @param $datas
     * @return array
     */
    public function formatOutPout($datas): array{
        $output = $datas;
        if(array_key_exists('avatar_url', $datas)){
            $output['picture'] = $datas['avatar_url'];
        }
        return $output;
    }

}