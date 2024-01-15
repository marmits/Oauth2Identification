<?php
declare(strict_types=1);

namespace Marmits\GoogleIdentification\Controller;

use Exception;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Marmits\GoogleIdentification\Providers\GoogleProvider;
use Marmits\GoogleIdentification\Providers\GithubProvider;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;



/**
 *
 */
class IndexController extends AbstractController
{


    protected GoogleProvider $googleProvider;
    protected GithubProvider $githubProvider;
    protected RequestStack $requestStack;

    /**
     * @param ContainerInterface $container
     * @param RequestStack $requestStack
     * @param GoogleProvider $googleProvider
     * @param GithubProvider $githubProvider
     */
    public function __construct(ContainerInterface $container, RequestStack $requestStack, GoogleProvider $googleProvider, GithubProvider $githubProvider)
    {
        $this->container = $container;
        $this->requestStack = $requestStack;
        $this->googleProvider = $googleProvider;
        $this->githubProvider = $githubProvider;
    }

    /**
     *
     * @Route("/bundle_index", name="bundle_index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->redirectToRoute('privat');
    }


    /**
     * Renvoi les données de base fournies par le provider enregistrées dans la session de l'utlisateur, une fois connecté.
     * @Route("/privat", name="privat")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function private(Request $request): Response
    {
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

        return $this->render('@MarmitsGoogleIdentification/private.html.twig', ['user' => $user]);
    }

    /**
     * Reset Session
     * @Route("/logout", name="logout")
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request): Response
    {
        if($this->requestStack->getSession()->has('access')){
            $this->requestStack->getSession()->remove('access');
        }
        if($this->requestStack->getSession()->has('oauth2state')){
            $this->requestStack->getSession()->remove('oauth2state');
        }
        if($this->requestStack->getSession()->has('provider_name')){
            $this->requestStack->getSession()->remove('provider_name');
        }
        $this->requestStack->getSession()->clear();

        return $this->redirectToRoute('bundle_index');

    }




}
