<?php
declare(strict_types=1);

namespace Marmits\GoogleIdentification\Services;

/**
 * OPENSSL
 */
Class Encryption
{

    protected array $params;

    /**
     * @param array params
     */
    public function __construct(array $params) {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function getParms(): array
    {
        return $this->params;
    }


    public function decrypt($data)
    {
        if($this->params['encryption_params']['decrypt']) {

            $key = hash('sha256', $this->params['encryption_params']['password']);
            $data = base64_decode($data);
            $ivSize = openssl_cipher_iv_length($this->params['encryption_params']['method']);
            $iv = substr($data, 0, $ivSize);
            if($data = openssl_decrypt(substr($data, $ivSize), $this->params['encryption_params']['method'], $key, OPENSSL_RAW_DATA, $iv)){
                return $data;
            }

            return 'Les données de la table sont incorrectes - decryptage KO';

        }

    }

    /**
     * @param string $data
     * @return string
     */
    public function encrypt(string $data): string
    {
        $key = hash('sha256', $this->params['encryption_params']['password']);

        $ivSize = openssl_cipher_iv_length($this->params['encryption_params']['method']);
        $iv = openssl_random_pseudo_bytes($ivSize);

        $encrypted = openssl_encrypt($data, $this->params['encryption_params']['method'], $key, OPENSSL_RAW_DATA, $iv);

        // For storage/transmission, we simply concatenate the IV and cipher text
        return base64_encode($iv . $encrypted);
    }

}