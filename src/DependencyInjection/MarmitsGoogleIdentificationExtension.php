<?php
declare(strict_types=1);

namespace Marmits\GoogleIdentification\DependencyInjection;

use Exception;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;


/**
 *
 */
class MarmitsGoogleIdentificationExtension extends Extension implements PrependExtensionInterface
{
    /**
     * Allow an extension to prepend the extension configurations.
     */
    public function prepend(ContainerBuilder $container)
    {


    }

    /**
     * Loads a specific configuration.
     *
     * @throws InvalidArgumentException|Exception When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerInterface $container)
    {

        $env = $container->getParameter('kernel.environment');

        $fileLocator = new FileLocator(__DIR__ . '/../Resources/config/packages');
        $loader = new YamlFileLoader($container, $fileLocator);
        $loader->load('marmits_google.yaml');

        $fileLocator = new FileLocator(__DIR__ . '/../Resources/config/packages');
        $loader = new YamlFileLoader($container, $fileLocator);

        foreach (['services'] as $basename )
        {
            $loader->load(sprintf('%s.yaml', $basename));
        }
        
    }
}