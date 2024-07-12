<?php
declare(strict_types=1);
namespace Marmits\Oauth2Identification\Controller;

use Exception;
use Marmits\Oauth2Identification\Providers\GithubProvider;
use Marmits\Oauth2Identification\Providers\GoogleProvider;
use Marmits\Oauth2Identification\Providers\Provider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


/**
 * Processus de demande d'authorisation et acces obtenu
 * protocol Oauth2 Goole et Github
 */

class OauthController extends AbstractController
{

    private Provider $provider;

    /**
     * @param ContainerInterface $container
     * @param Provider $provider
     */
    public function __construct(
        ContainerInterface $container,
        Provider $provider
    )
    {
        $this->container = $container;
        $this->provider = $provider;
    }

    /*
     * ####################### requete provider #####################
     */
    /**
     * Demande d'autorisation a github
     * @return Response
     * @throws Exception
     */
    #[Route('github_authorize', name: 'github_authorize')]
    public function githubgetauthorize(): Response
    {
        $provider = $this->provider->get(GithubProvider::PROVIDER_NAME);
        return $provider->getauthorize();
    }

    /**
     * Processus Authorization Code Grant
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('getaccesstokengithub', name: 'getaccesstokengithub')]
    public function getaccesstokenGithub(Request $request): JsonResponse
    {
        $provider = $this->provider->get(GithubProvider::PROVIDER_NAME);
        return $provider->getaccesstoken($request);
    }

    /**
     * Demande d'autorisation a google
     * @return Response
     * @throws Exception
     */
    #[Route('google_authorize', name: 'google_authorize')]
    public function googlegetauthorize(): Response
    {
        $provider = $this->provider->get(GoogleProvider::PROVIDER_NAME);
        return $provider->getauthorize();
    }

    /**
     * Processus Authorization Code Grant
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('getaccesstokengoogle', name: 'getaccesstokengoogle')]
    public function getaccesstokenGoogle(Request $request): JsonResponse
    {
        $provider = $this->provider->get(GoogleProvider::PROVIDER_NAME);
        return $provider->getaccesstoken($request);
    }

}