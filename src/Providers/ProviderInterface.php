<?php
declare(strict_types=1);
namespace Marmits\Oauth2Identification\Providers;


/**
 *
 */
interface ProviderInterface
{
    /**
     * Function to check wether this implementation supports the $type
     */
    public function supports(string $type): bool;

    /**
     * This function contains the actual logic
     */
    public function build(): AbstractProvider;

}