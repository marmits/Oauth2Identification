<?php
declare(strict_types=1);

namespace Maximo\Adresse;

use Symfony\Component\HttpKernel\Bundle\Bundle;


/**
 *
 */
class MaximoAdresseBundle extends Bundle
{
    public function __construct(){
        date_default_timezone_set('Europe/Paris');
    }

}


