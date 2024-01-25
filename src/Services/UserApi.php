<?php

namespace Marmits\Oauth2Identification\Services;

use Exception;
use Marmits\Oauth2Identification\Providers\GithubProvider;
use Marmits\Oauth2Identification\Providers\GoogleProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class UserApi
{

    protected GoogleProvider $googleProvider;
    protected GithubProvider $githubProvider;
    protected RequestStack $requestStack;


    public function __construct(
        RequestStack $requestStack,
        GoogleProvider $googleProvider,
        GithubProvider $githubProvider
    )
    {
        $this->requestStack = $requestStack;
        $this->googleProvider = $googleProvider;
        $this->githubProvider = $githubProvider;
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
        return $this->provideName;
    }


    /**
     * @param $access
     * @return void
     */
    public function setOauthUserIdentifiants($access) : void{
        $this->requestStack->getSession()->set('oauth_user_infos',$access);
    }


    /**
     * @return array
     */
    public function getOauthUserIdentifiants(): array{
        if($this->requestStack->getSession()->has('oauth_user_infos')) {
            return [
                'email' => $this->requestStack->getSession()->get('oauth_user_infos')['email'],
                'api_user_id' => $this->requestStack->getSession()->get('oauth_user_infos')['api_user_id'],
                'accesstoken' => $this->requestStack->getSession()->get('oauth_user_infos')['accesstoken']
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
            $datas_access = $this->requestStack->getSession()->get('oauth_user_infos');
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