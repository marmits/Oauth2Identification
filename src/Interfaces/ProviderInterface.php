<?php

namespace Maximo\Adresse\Interfaces;


use Maximo\Adresse\Provider\AbstractBuilder;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

interface ProviderInterface
{

    /**
     * Function to check wether this implementation supports the $type
     */
    public function supports(string $type): bool;

    /**
     * This function contains the actual logic
     */
    public function build(LoggerInterface $logger,  $apiConnector, array $adresse_params): AbstractBuilder;

}
