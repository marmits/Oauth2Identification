<?php
declare(strict_types=1);

namespace Marmits\Oauth2Identification\Services;
Use Exception;

/**
 * OPENSSL
 */
Class Encryption
{

    protected array $params;

    /**
     * @param array $params params
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


    /**
     * @throws Exception
     */
    public function decrypt($data)
    {
        if($this->params['encryption_params']['decrypt']) {
            $message_error = 'Les données de la table sont incorrectes - decryptage KO';
            $key = hash('sha256', $this->params['encryption_params']['key']);
            $data = base64_decode($data);
            $ivSize = openssl_cipher_iv_length($this->params['encryption_params']['method']);
            $iv = substr($data, 0, $ivSize);

            if(substr($data, $ivSize) === ''){
                throw New Exception($message_error);
            }

            if($data = openssl_decrypt(substr($data, $ivSize), $this->params['encryption_params']['method'], $key, OPENSSL_RAW_DATA, $iv)){
                return $data;
            }

            return $message_error;

        }

    }

    /**
     * @param string $data
     * @return string
     */
    public function encrypt(string $data): string
    {
        $key = hash('sha256', $this->params['encryption_params']['key']);

        $ivSize = openssl_cipher_iv_length($this->params['encryption_params']['method']);
        $iv = openssl_random_pseudo_bytes($ivSize);

        $encrypted = openssl_encrypt($data, $this->params['encryption_params']['method'], $key, OPENSSL_RAW_DATA, $iv);

        // For storage/transmission, we simply concatenate the IV and cipher text
        return base64_encode($iv . $encrypted);
    }

}