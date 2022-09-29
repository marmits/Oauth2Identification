<?php
declare(strict_types=1);
namespace Maximo\Adresse\Controller;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Maximo\Adresse\Controller\AdresseController;
use Maximo\Adresse\Service\ProviderService;

/**
 *
 */
class LoadAdresse extends AdresseController
{

    /**
     * @param ContainerInterface $container
     * @param ProviderService $providerService
     */
    public function __construct(ContainerInterface $container, ProviderService $providerService)
    {
        parent::__construct($container, $providerService);
    }

    /**
     *
     * @Route("/getconfigadresse", options={"expose"=true}, name="context_adresse")
     * @param Request $request
     * @return JsonResponse
     */
    public function configAdresse(Request $request): JsonResponse
    {
        if(($request->get('paramsInput') !== null) && (!empty($request->get('paramsInput')))){
            $paramsInput = json_decode($request->get('paramsInput'), true);
            return JsonResponse::fromJsonString(json_encode($this->provider->LoadDatasForJs($paramsInput)), 200);
        }

    }

}
