<?php
declare(strict_types=1);
namespace Maximo\Adresse\Provider;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Maximo\ApiConnector\Api\CentralConnector;
use Maximo\Adresse\Provider\AbstractBuilder;


class CentralBuilder extends AbstractBuilder
{

    protected LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     * @param CentralConnector $apiConnector
     * @param array $adresse_params
     */
    public function __construct(LoggerInterface $logger, CentralConnector $apiConnector, array $adresse_params)
    {
        $this->setNumClient($adresse_params['adresse_params']['input_default']['currentClientId']);
        parent::__construct($logger,  $apiConnector, $adresse_params);
    }

    /**
     * @param array $params
     */
    public function setConfigApiConnector(array $params): AbstractBuilder{
        $ApiConnectorParams = [];
        return $this;
    }

    
    

}
