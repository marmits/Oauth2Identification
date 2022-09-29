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
    protected $api76310Connector;
    protected $apiCentralConnector;

    /**
     * @param ContainerInterface $container
     * @param ProviderService $provider
     */
    public function __construct(ContainerInterface $container, ProviderService $providerService)
    {
        $this->container = $container;
        $this->provider = $providerService->execute($providerService->getTypeProvider());

        $this->apiCentralConnector = $providerService->getApiCentralConnector();
        $this->api76310Connector = $providerService->getApi76310Connector();



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

    /**
     *
     * @Route("/gettokenapi76310", options={"expose"=true}, name="gettokenapi76310", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getTokenApi76310(Request $request): JsonResponse
    {
        $access_token = $this->api76310Connector->getAcessToken();
        return new JsonResponse($access_token, 200);
    }

    /**
     *
     * @Route("/getsuggestion76310", options={"expose"=true}, name="getsuggestion76310", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getSuggestion7631(Request $request): JsonResponse
    {
        $access_token = $request->request->get("access_token");
        $values = $request->request->get("values");
        $datas = $this->api76310Connector->getSuggestion($access_token, $values);
        return new JsonResponse($datas, 200);

    }

    /**
     *
     * @Route("/getdetailsadresse76310", options={"expose"=true}, name="getdetailsadresse76310", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getDetailsAdresse7631(Request $request): JsonResponse
    {
        $access_token = $request->request->get("access_token");
        $values = $request->request->get("values");
        $datas = $this->api76310Connector->getDetailsAdresse($access_token, $values);
        return new JsonResponse($datas, 200);

    }


    /**
     * @Route("/getcitybyzipcode/{zipcode}/RecuperationVilleParCodePostalCentralise", options={"expose"=true}, name="completeCityByZipCodeCentralise", methods={"GET"})
     * @param $zipcode
     * @return JsonResponse
     */
    public function completeCityByZipCodeCentralise($zipcode)
    {
        $cities = $this->apiCentralConnector->getCityByZipCode($zipcode);
        return new JsonResponse($cities);
    }

    /**
     * @Route("/getcityroadbyzipcode/{zipcode}/{city}/RecuperationRueParCodePostalVilleCentralise", options={"expose"=true}, name="completeCityRoadByZipCodeCentralise", methods={"GET"})
     * @param $zipcode
     * @param $city
     * @return JsonResponse
     */
    public function completeCityRoadByZipCodeCentralise($zipcode,$city)
    {
        $roads = $this->apiCentralConnector->getCityRoadByZipCode($zipcode,$city);
        return new JsonResponse($roads);
    }

    
    
    

}
