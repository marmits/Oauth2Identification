<?php
declare(strict_types=1);

namespace Marmits\Oauth2Identification\Dto;

/**
 * Elements de la table pouvant servir d'identifiants
 */
class IdentifiantsOutput
{
    public string $email;
    public string $api_user_id;
}