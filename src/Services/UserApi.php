<?php
declare(strict_types=1);
namespace Marmits\Oauth2Identification\Services;

use DateTimeImmutable;
use Exception;
use Marmits\Oauth2Identification\Dto\AccessInput;
use Marmits\Oauth2Identification\Dto\IdentifiantsOutput;
use Marmits\Oauth2Identification\Entity\OauthUser;
use Marmits\Oauth2Identification\Providers\Provider;
use Marmits\Oauth2Identification\Repository\OauthUserRepository;
use Symfony\Component\HttpFoundation\RequestStack;


/**
 *
 */
class UserApi
{

    private Provider $provider;
    private OauthUserRepository $oauthUserRepository;
    protected RequestStack $requestStack;
    public Encryption $encryption;

    /**
     * @param RequestStack $requestStack
     * @param Provider $provider
     * @param OauthUserRepository $oauthUserRepository
     * @param Encryption $encryption
     */
    public function __construct(
        RequestStack $requestStack,
        Provider $provider,
        OauthUserRepository $oauthUserRepository,
        Encryption $encryption
    )
    {
        $this->requestStack = $requestStack;
        $this->provider = $provider;
        $this->oauthUserRepository = $oauthUserRepository;
        $this->encryption = $encryption;
    }

    /**
     * sauvegarde en bdd l'utilisateur oauth puis met en session son id
     * @param AccessInput $access
     * @return void
     * @throws Exception
     */
    public function setOauthUser(AccessInput $access) : void{
        $id = $this->saveOauthUser($access);
        $this->requestStack->getSession()->set('oauthUserId',$id);
    }

    /**
     * Permet de récupérer en base l'email et l'identifiant du user oauth avec l'id en session
     * @return IdentifiantsOutput|null
     */
    public function getOauthUserIdentifiants(): ?IdentifiantsOutput{

        if($this->requestStack->getSession()->has('oauthUserId')) {
            $idoauthUser = $this->requestStack->getSession()->get('oauthUserId');
            $userOauth = $this->oauthUserRepository->find($idoauthUser);
            $output = new IdentifiantsOutput();
            $output->email = $userOauth->getEmail();
            $output->api_user_id = $userOauth->getIdApiUser();
            return $output;
        }

        return null;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function fetch() : array{
        $user = [];

        if($this->requestStack->getSession()->has('oauthUserId')) {
            $idoauthUser = $this->requestStack->getSession()->get('oauthUserId');
            $userOauth = $this->oauthUserRepository->find($idoauthUser);
            $accessInput = new AccessInput();
            $accessInput->accesstoken = $userOauth->getAccessToken();
            $accessInput->refreshtoken = $userOauth->getRefreshToken();
            $accessInput->api_user_id = $userOauth->getIdApiUser();
            $accessInput->email = $userOauth->getEmail();
            $accessInput->ownerDetails = $userOauth->getOwnerDetails();
            $provider = $this->provider->get($userOauth->getProviderName());
            $user = $provider->fetchUser($accessInput);
        }
        return $user;
    }

    /**
     * @return void
     */
    public function killSession(): void{
        $this->requestStack->getSession()->clear();
    }

    /**
     * @param AccessInput $access
     * @return int
     * @throws Exception
     */
    private function saveOauthUser(AccessInput $access): int {
        $crit = ['email' => $access->email, 'idApiUser' => $access->api_user_id];
        $userOauth = $this->oauthUserRepository->findBy($crit);
        if(count($userOauth) > 1){
            throw new Exception('Plusieurs utlisateurs trouvées avec '. json_encode($crit,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        }
        if(count($userOauth) === 0){
            $userOauth = new OauthUser();
        }
        elseif(count($userOauth) === 1){
            $userOauth = $this->oauthUserRepository->findOneBy($crit);
        }
        $userOauth->setProviderName($access->provider_name);
        $userOauth->setIdApiUser($access->api_user_id);
        $userOauth->setEmail($access->email);
        $userOauth->setAccessToken($access->accesstoken);
        $userOauth->setRefreshToken($access->refreshtoken);
        $userOauth->setOwnerDetails($access->ownerDetails);
        $time = new DateTimeImmutable();
        $userOauth->setDateConnexion($time);
        return $this->oauthUserRepository->add($userOauth, true);
    }

}