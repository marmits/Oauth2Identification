<?php

namespace Maximo\Adresse\Provider;

use Maximo\Adresse\Interfaces\ProviderInterface;
use Maximo\Adresse\Provider\AbstractBuilder;



class CentralProvider implements ProviderInterface
{


    public function supports(string $type): bool
    {
        return 'central' === $type;
    }

    public function build( $logger,   $apiConnector, array $adresse_params):AbstractBuilder {
        $builder = new CentralBuilder($logger, $apiConnector, $adresse_params);
        return $builder;
    }


}
