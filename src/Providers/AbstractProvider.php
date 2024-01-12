<?php
declare(strict_types=1);

namespace Marmits\GoogleIdentification\Providers;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 *
 */
abstract class AbstractProvider
{

    protected $client;

    /**
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param ResponseInterface $response
     * @return array
     * @throws Exception
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
        $content = 'vide';
        if($response->getContent() !== null){
            $content = $response->getContent();
        }
        $contentType = $response->getHeaders()['content-type'][0];

        return $response->toArray();
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