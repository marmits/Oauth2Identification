<?php
declare(strict_types=1);
namespace Marmits\Oauth2Identification\Services;

use Exception;
use Marmits\Oauth2Identification\Providers\GithubProvider;
use Marmits\Oauth2Identification\Providers\GoogleProvider;
use Marmits\Oauth2Identification\Providers\ProviderService;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 *
 */
class UserApi
{

    private ProviderService $providerService;
    protected RequestStack $requestStack;
    public Encryption $encryption;


    /**
     * @param RequestStack $requestStack
     * @param ProviderService $providerService
     * @param Encryption $encryption
     */
    public function __construct(
        RequestStack $requestStack,
        ProviderService $providerService,
        Encryption $encryption
    )
    {
        $this->requestStack = $requestStack;
        $this->providerService = $providerService;
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
     * @throws Exception
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
     * @return array
     * @throws Exception
     */
    public function fetch() : array{
        $user = [];
        if($this->requestStack->getSession()->has('oauth_user_infos')){
            $datas_access = json_decode($this->encryption->decrypt($this->requestStack->getSession()->get('oauth_user_infos')), true);
            if($this->requestStack->getSession()->has('provider_name')) {
                $provider = $this->providerService->execute($this->requestStack->getSession()->get('provider_name'));
                $user = $provider->fetchUser($datas_access);
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