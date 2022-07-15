<?php
namespace Maximo\Adresse\Service;


use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Maximo\Adresse\Interfaces\ProviderInterface;
use Maximo\Adresse\Provider\AbstractBuilder;

class ProviderService
{

    protected iterable $providers;
    protected LoggerInterface $logger;
    protected $apiConnector;
    protected array $adresse_params;

    protected AbstractBuilder $provider;

    public function __construct(iterable $providers, LoggerInterface $logger,  $apiConnector, array $adresse_params)
    {
        $this->providers =  $providers;
        $this->logger = $logger;
        $this->apiConnector = $apiConnector;
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


}
