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
class ClientController extends AbstractController
{

    private ProviderService $providerService;
    private $provider;
    /**
     * @param ContainerInterface $container
     * @param ProviderService $providerService
     */
    public function __construct(ContainerInterface $container, ProviderService $providerService)
    {
        $this->container = $container;
        $this->provider = $providerService->execute($providerService->getTypeProvider());
    }

    /**
     * @param Request $request
     * @param $numcli
     * @param $type_adresse
     * @return JsonResponse
     * @Route("/clientcentral/informations/{numcli}/{type_adresse}/informationClient", name="get_central_information_client", options={"expose"=true}, methods={"POST"})
     */
    public function informationClient(Request $request, $numcli, $type_adresse): JsonResponse
    {
        $columns = $request->request->get('columns');
        return $this->provider->getClientInformation($numcli,$type_adresse, $columns);
    }

}
