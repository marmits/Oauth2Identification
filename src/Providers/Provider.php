<?php
declare(strict_types=1);

namespace Marmits\Oauth2Identification\Providers;


use Exception;

/**
 * Definit le provider en fonction de son nom
 */
class Provider
{

    protected iterable $providers;

    /**
     * @param iterable $providers
     */
    public function __construct(
        iterable $providers
    )
    {
        $this->providers = $providers;
    }

    /**
     * @param $provider_name
     * @return AbstractProvider
     * @throws Exception
     */
    public function get($provider_name): AbstractProvider
    {
        foreach($this->providers as $provider) {
            if ($provider->supports($provider_name)) {
                return $provider->build();
            }
        }

        throw new Exception("Unsupported  type $provider_name");
    }

}