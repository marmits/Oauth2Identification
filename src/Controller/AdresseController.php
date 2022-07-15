<?php

namespace Maximo\Adresse\Controller;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use Symfony\Component\ErrorHandler\Error\ClassNotFoundError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Maximo\Adresse\Service\ProviderService;

/**
 *
 */
class AdresseController extends AbstractController
{

    protected $provider;
    /**
     * @param ContainerInterface $container
     * @param ProviderService $provider
     */
    public function __construct(ContainerInterface $container, ProviderService $providerService)
    {
        $this->container = $container;
        $this->provider = $providerService->execute($providerService->getTypeProvider());


    }

    /**
     *
     * @Route("/bundle_adresse", name="bundle_adresse")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->render('@MaximoAdresse/default.html.twig', [
            'test' => 'test'
        ]);
    }

}
