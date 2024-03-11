<?php
declare(strict_types=1);

namespace Marmits\Oauth2Identification\Controller;

use Marmits\Oauth2Identification\Services\UserApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class ApiTestController extends AbstractController
{
    protected UserApi $userApi;
    protected RequestStack $requestStack;

    public function __construct(
        ContainerInterface $container,
        RequestStack $requestStack,
        UserApi $userApi
    )
    {
        $this->container = $container;
        $this->requestStack = $requestStack;
        $this->userApi = $userApi;

    }
    /**
     * @Route("/api/test/{name}", name="api_test")")
     */
    public function apiHelloword(string $name): JsonResponse
    {
        return new JsonResponse('hello ' . $name);
    }
}