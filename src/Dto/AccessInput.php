<?php
declare(strict_types=1);

namespace Marmits\Oauth2Identification\Dto;

/**
 * Elements à enregistrer en BDD concernant l'utilisateur connecté
 */
class AccessInput
{
    public string $provider_name;
    public ?array $ownerDetails;
    public string $accesstoken;
    public ?string $refreshtoken;
    public string $email;
    public string $api_user_id;
}