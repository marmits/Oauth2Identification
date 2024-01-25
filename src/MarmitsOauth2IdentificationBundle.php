<?php
declare(strict_types=1);

namespace Marmits\Oauth2Identification;

use Symfony\Component\HttpKernel\Bundle\Bundle;


/**
 *
 */
class MarmitsOauth2IdentificationBundle extends Bundle
{
    public function __construct(){
        date_default_timezone_set('Europe/Paris');
    }

}


