<?php
namespace Marmits\GoogleIdentification\Services;


Class Tools
{
   

    protected array $params;

    /**
     * @param array params
     */
    public function __construct(array $params) {
        $this->params = $params;
    }


    public function decrypt($data)
    {

        $key = hash('sha256', $this->params["tools_params"]['password']);
        $data = base64_decode($data);
        $ivSize = openssl_cipher_iv_length($this->params["tools_params"]['method']);
        $iv = substr($data, 0, $ivSize);
        $data = openssl_decrypt(substr($data, $ivSize), $this->params["tools_params"]['method'], $key, OPENSSL_RAW_DATA, $iv);

        return $data;
    }


}