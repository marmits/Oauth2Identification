<?php

namespace Marmits\Oauth2Identification\Services;

use Exception;
use Marmits\Oauth2Identification\Providers\GithubProvider;
use Marmits\Oauth2Identification\Services\Encryption;
use Marmits\Oauth2Identification\Providers\GoogleProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class UserApi
{

    protected GoogleProvider $googleProvider;
    protected GithubProvider $githubProvider;
    protected RequestStack $requestStack;
    public Encryption $encryption;


    public function __construct(
        RequestStack $requestStack,
        GoogleProvider $googleProvider,
        GithubProvider $githubProvider,
        Encryption $encryption
    )
    {
        $this->requestStack = $requestStack;
        $this->googleProvider = $googleProvider;
        $this->githubProvider = $githubProvider;
        $this->encryption = $encryption;
    }



    /**
     * @param string $providerName
     */
    public function setProviderName(string $providerName): void
    {
        $this->requestStack->getSession()->set('provider_name', $providerName);
    }

    /**
     * @return string
     */
    public function getProvideName(): string
    {
        return $this->requestStack->getSession()->get('provider_name');
    }


    /**
     * @param $access
     * @return void
     */
    public function setOauthUserIdentifiants($access) : void{
        $this->requestStack->getSession()->set('oauth_user_infos',$this->encryption->encrypt(json_encode($access)));
    }


    /**
     * @return array
     */
    public function getOauthUserIdentifiants(): array{
        if($this->requestStack->getSession()->has('oauth_user_infos')) {
            $oauth_user_infos = json_decode($this->encryption->decrypt($this->requestStack->getSession()->get('oauth_user_infos')), true);
            return [
                'email' => $oauth_user_infos['email'],
                'api_user_id' => $oauth_user_infos['api_user_id']
            ];
        }
        return [];
    }

    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function fetch(Request $request) : array{
        $user = [];
        if($this->requestStack->getSession()->has('oauth_user_infos')){
            $datas_access = json_decode($this->encryption->decrypt($this->requestStack->getSession()->get('oauth_user_infos')), true);
            if($this->requestStack->getSession()->has('provider_name')) {
                switch ($this->requestStack->getSession()->get('provider_name')){
                    case $this->githubProvider->getName():
                        $user = $this->githubProvider->fetchUser($datas_access);
                        break;
                    case $this->googleProvider->getName():
                        $user = $this->googleProvider->fetchUser($datas_access);
                        break;
                }
            }
        }
        return $user;
    }

    /**
     * @return void
     */
    public function killSession(): void{
        $this->requestStack->getSession()->clear();
    }

}