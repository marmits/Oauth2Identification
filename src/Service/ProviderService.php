<?php
namespace Maximo\Adresse\Service;


use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Maximo\Adresse\Interfaces\ProviderInterface;
use Maximo\Adresse\Provider\AbstractBuilder;

use Maximo\ApiConnector\Api\CentralConnector;
use Maximo\ApiConnector\Api\Api76310Connector;


class ProviderService
{

    protected iterable $providers;
    protected LoggerInterface $logger;
    protected CentralConnector $apiConnector;
    protected Api76310Connector $api76310Connector;

    protected array $adresse_params;

    protected AbstractBuilder $provider;

    public function __construct(iterable $providers, LoggerInterface $logger,  CentralConnector $apiConnector, Api76310Connector $api76310Connector, array $adresse_params)
    {
        $this->providers =  $providers;
        $this->logger = $logger;
        $this->apiConnector = $apiConnector;
        $this->api76310Connector = $api76310Connector;
        $this->adresse_params = $adresse_params;
    }

    public function execute(String $type): AbstractBuilder
    {
        foreach($this->providers as $provider) {

            if ($provider->supports($this->adresse_params['adresse_params']['provider'])) {
                $this->provider =  $provider->build($this->logger, $this->apiConnector, $this->adresse_params);
                return $this->provider;

            }
        }
        throw new Exception("Unsupported  type $type");

    }

    public function getTypeProvider(){
        return $this->adresse_params['adresse_params']['provider'];
    }


    public function getApi76310Connector(){
        $this->api76310Connector->setProd($this->adresse_params['adresse_params']['api76310']['prod']);
        return $this->api76310Connector;
    }


    public function getApiCentralConnector(){
        return $this->apiConnector;
    }



}
