<?php
declare(strict_types=1);
namespace Marmits\Oauth2Identification\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Marmits\Oauth2Identification\Services\UserApi;

/**
 *
 */
class userApiController extends AbstractController
{

    protected UserApi $userApi;

    /**
     * @param ContainerInterface $container
     * @param UserApi $userApi
     */
    public function __construct(
        ContainerInterface $container,
        UserApi $userApi
    )
    {
        $this->container = $container;
        $this->userApi = $userApi;
    }

    /**
     * Renvoi les identifiants attendus (email et id_api_user) de l'utlisateur oauth connecté
     * @return JsonResponse
     * @throws Exception
     */
    // getUserOauthLogged
    #[Route(path: '/getuseroauthlogged', name: 'getuseroauthlogged', methods: ['GET'])]
    public function getUserOauthLogged(): JsonResponse
    {
        if($this->userApi->getOauthUserIdentifiants() !== null){
            return new jsonResponse(
                [
                    'code'=> 200, 'message' => 'ok authorisation'
                ], 200);
        }
        return new jsonResponse(['code'=> 401, 'message' => 'Accès interdit'], 401);
    }
}