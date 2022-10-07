<?php

namespace Marmits\GoogleIdentification\Services;
use League\OAuth2\Client\Provider\Google;

/**
 *
 */
class GoogleProvider
{

    protected array $params;

    /**
     * @param array $googleclient_params
     */
    public function __construct(array $googleclient_params)
    {
        $this->params = $googleclient_params;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return Google
     */
    public function getInstance(): Google
    {

        return new Google([
            'clientId'     => $this->params['googleclient_params']['client_id'],
            'clientSecret' => $this->params['googleclient_params']['client_secret'],
            'redirectUri'  => $this->params['googleclient_params']['redirect_uris'],
            'hostedDomain' => $this->params['googleclient_params']['google_origins']
        ]);

    }

}