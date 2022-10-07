<?php
declare(strict_types=1);

namespace Marmits\GoogleIdentification;

use Symfony\Component\HttpKernel\Bundle\Bundle;


/**
 *
 */
class MarmitsGoogleIdentificationBundle extends Bundle
{
    public function __construct(){
        date_default_timezone_set('Europe/Paris');
    }

}


