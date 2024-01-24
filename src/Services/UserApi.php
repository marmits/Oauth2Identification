<?php

namespace Marmits\GoogleIdentification\Services;

use Marmits\GoogleIdentification\Providers\GithubProvider;
use Marmits\GoogleIdentification\Providers\GoogleProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class UserApi
{

    protected GoogleProvider $googleProvider;
    protected GithubProvider $githubProvider;
    protected RequestStack $requestStack;

    public function __construct(
        RequestStack $requestStack, GoogleProvider $googleProvider, GithubProvider $githubProvider
    )
    {
        $this->requestStack = $requestStack;
        $this->googleProvider = $googleProvider;
        $this->githubProvider = $githubProvider;
    }

    public function fetch(Request $request) : array{
        $user = [];
        if($this->requestStack->getSession()->has('access')){
            $datas_access = $this->requestStack->getSession()->get('access');

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

}