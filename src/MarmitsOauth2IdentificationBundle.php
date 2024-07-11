<?php
declare(strict_types=1);

namespace Marmits\Oauth2Identification;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;



/**
 *
 */
class MarmitsOauth2IdentificationBundle extends AbstractBundle
{
    public function __construct(){
        date_default_timezone_set('Europe/Paris');
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {

        $container->import('../config/services.yaml');
        $container->import('../config/marmits_clientapi.yaml');

    }

}


