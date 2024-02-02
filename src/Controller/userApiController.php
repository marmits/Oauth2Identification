<?php
declare(strict_types=1);
namespace Marmits\Oauth2Identification\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
     * Renvoi l'utilisateur autorisé son email et l'access renovoyé stocké dans la session
     * @Route("/getuseroauthlogged", options={"expose"=true}, name="getuseroauthlogged", methods={"GET"})
     * @return JsonResponse
     * @throws Exception
     */

    // getUserOauthLogged
    public function getUserOauthLogged(): JsonResponse
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