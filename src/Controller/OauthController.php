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
use Symfony\Component\Routing\Annotation\Route;


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
     * @Route("/github_authorize", name="github_authorize")
     * @return Response
     * @throws Exception
     */
    public function githubgetauthorize(): Response
    {
        $provider = $this->provider->execute(GithubProvider::PROVIDER_NAME);
        return $provider->getauthorize();
    }

    /**
     * Processus Authorization Code Grant
     * @Route("/getaccesstokengithub", name="getaccesstokengithub")
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function getaccesstokenGithub(Request $request): JsonResponse
    {
        $provider = $this->provider->execute(GithubProvider::PROVIDER_NAME);
        return $provider->getaccesstoken($request);
    }

    /**
     * Demande d'autorisation a google
     * @Route("/google_authorize", name="google_authorize")
     * @return Response
     * @throws Exception
     */
    public function googlegetauthorize(): Response
    {
        $provider = $this->provider->execute(GoogleProvider::PROVIDER_NAME);
        return $provider->getauthorize();
    }

    /**
     * Processus Authorization Code Grant
     * @Route("/getaccesstokengoogle", name="getaccesstokengoogle")
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function getaccesstokenGoogle(Request $request): JsonResponse
    {
        $provider = $this->provider->execute(GoogleProvider::PROVIDER_NAME);
        return $provider->getaccesstoken($request);
    }

}