<?php
declare(strict_types=1);
namespace Marmits\Oauth2Identification\Controller;

use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Route("/bundle_index", name="bundle_index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->redirectToRoute('bundle_private');
    }

    /**
     * Redirection bundle_index route
     * Reset Session
     * @Route("/logout", name="logout")
     * @return Response
     */
    public function logout(): Response
    {
        $this->userApi->killSession();
        return $this->redirectToRoute('bundle_index');
    }

    /**
     * Rendu des données de base fournies par le provider enregistrées dans la session de l'utlisateur, une fois connecté.
     * MarmitsOauth2Identification/bundle_private.html.twig
     * @Route("/bundle_private", name="bundle_private")
     * @return Response
     * @throws Exception
     */
    public function bundlePrivate(): Response
    {
        $user = $this->userApi->fetch();
        return $this->render('@MarmitsOauth2Identification/privateDefault.html.twig', ['user' => $user]);
    }

}
