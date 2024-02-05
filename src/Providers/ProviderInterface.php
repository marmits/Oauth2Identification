<?php
declare(strict_types=1);
namespace Marmits\Oauth2Identification\Providers;

use League\OAuth2\Client\Provider\AbstractProvider as LeagueProvider;
use Marmits\Oauth2Identification\Dto\AccessInput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 */
interface ProviderInterface
{
    /**
     * Function to check wether this implementation supports the $type
     */
    public function supports(string $type): bool;

    /**
     * This function contains the actual logic
     */
    public function build(): AbstractProvider;

    /**
     * @return LeagueProvider
     */
    public function getInstance(): LeagueProvider;

    /**
     * @param Request $request
     * @return JsonResponse
     */
     public function getaccesstoken(Request $request): JsonResponse;

    /**
     * @return Response
     */
     public function getauthorize(): Response;

    /**
     * @param AccessInput $datas_access
     * @return array
     */
     public function fetchUser(AccessInput $datas_access): array;

}