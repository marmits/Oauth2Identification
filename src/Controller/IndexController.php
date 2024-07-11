<?php
declare(strict_types=1);
namespace Marmits\Oauth2Identification\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Marmits\Oauth2Identification\Services\UserApi;



/**
 *
 */
class IndexController extends AbstractController
{

    protected UserApi $userApi;
    protected RequestStack $requestStack;

    /**
     * @param ContainerInterface $container
     * @param RequestStack $requestStack
     * @param UserApi $userApi
     */
    public function __construct(
        ContainerInterface $container,
        RequestStack $requestStack,
        UserApi $userApi
    )
    {
        $this->container = $container;
        $this->requestStack = $requestStack;
        $this->userApi = $userApi;
    }

    /**
     * Redirection privat route
     * @param Request $request
     * @return Response
     */
    #[Route('bundle_index', name: 'bundle_index')]
    public function index(Request $request): Response
    {
        return $this->redirectToRoute('privat');
    }

    /**
     * Redirection bundle_index route
     * Reset Session
     * @return Response
     */
    #[Route('logout', name: 'logout')]
    public function logout(): Response
    {
        $this->userApi->killSession();
        return $this->redirectToRoute('bundle_index');
    }

    /**
     * Rendu des données de base fournies par le provider enregistrées dans la session de l'utlisateur, une fois connecté.
     * MarmitsOauth2Identification/bundle_private.html.twig
     * @return Response
     * @throws Exception
     */
    #[Route('privat', name: 'privat')]
    public function bundlePrivate(): Response
    {
        $user = $this->userApi->fetch();
        return $this->render('@MarmitsOauth2Identification/privateDefault.html.twig', ['user' => $user]);
    }


    /**
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('api/user/datas', name: 'api_user_datas')]
    public function apiUserFetch(): JsonResponse{
        return new JsonResponse($this->userApi->fetch());
    }

}
