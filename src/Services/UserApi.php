<?php
declare(strict_types=1);
namespace Marmits\Oauth2Identification\Services;

use Exception;

use Marmits\Oauth2Identification\Providers\Provider;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 *
 */
class UserApi
{

    private Provider $provider;
    protected RequestStack $requestStack;
    public Encryption $encryption;


    /**
     * @param RequestStack $requestStack
     * @param Provider $provider
     * @param Encryption $encryption
     */
    public function __construct(
        RequestStack $requestStack,
        Provider $provider,
        Encryption $encryption
    )
    {
        $this->requestStack = $requestStack;
        $this->provider = $provider;
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
     * @throws Exception
     */
    public function getProviderName(): string
    {
        if($this->requestStack->getSession()->has('provider_name')) {
            return $this->requestStack->getSession()->get('provider_name');
        }
        return '';

    }

    /**
     * @param $access
     * @return void
     */
    public function setOauthUserIdentifiants($access) : void{
        $this->requestStack->getSession()->set('oauth_user_infos',$this->encryption->encrypt(json_encode($access)));
    }


    /**
     * Permet de récupéré dans la session l'email et api_user_id connecté
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
        if($this->getProviderName() !== '') {
            if ($this->requestStack->getSession()->has('oauth_user_infos')) {
                $provider = $this->provider->execute($this->getProviderName());
                $datas_access = json_decode($this->encryption->decrypt($this->requestStack->getSession()->get('oauth_user_infos')), true);
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