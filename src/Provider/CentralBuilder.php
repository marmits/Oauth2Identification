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

        $options['central_connector_enable_debug'] = $apiConnector->getEnableDebug();
        $this->setConfigApiConnector($adresse_params, $options);


        parent::__construct($logger,  $apiConnector,  $this->getConfigApiConnector());
    }
    
}
