<?php
namespace Marmits\Oauth2Identification\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Marmits\Oauth2Identification\Services\UserApi;

class userApiController extends AbstractController
{

    protected UserApi $userApi;
    public function __construct(
        ContainerInterface $container,
        UserApi $userApi
    )
    {
        $this->container = $container;
        $this->userApi = $userApi;
    }

    /**
     * Renvoi l'utilisateur autorisé son email et l'access renovoyé stocké dans la session
     * @Route("/getuseroauthlogged", options={"expose"=true}, name="getuseroauthlogged", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */

    // getUserOauthLogged
    public function getUserOauthLogged(Request $request): JsonResponse
    {
        if(!empty($this->userApi->getOauthUserIdentifiants())){
            return new jsonResponse(
                [
                    'code'=> 200, 'message' => 'ok authorisation'
                ], 200);
        }
        return new jsonResponse(['code'=> 401, 'message' => 'Accès interdit'], 401);
    }
}