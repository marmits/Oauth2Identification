<?php
declare(strict_types=1);
namespace Marmits\Oauth2Identification\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use Marmits\Oauth2Identification\Providers\AccessTokenProcess;


/**
 * Processus de demande d'authorisation et acces obtenu
 * protocol Oauth2 Goole et Github
 */

class OauthController extends AbstractController
{
    private RequestStack $requestStack;
    private AccessTokenProcess $accessTokenProcess;

    /**
     * @param ContainerInterface $container
     * @param AccessTokenProcess $accessTokenProcess
     */
    public function __construct(
        ContainerInterface $container,
        AccessTokenProcess $accessTokenProcess
    )
    {
        $this->container = $container;
        $this->accessTokenProcess = $accessTokenProcess;
    }

    /*
     * ####################### requete provider #####################
     */
    /**
     * Demande d'autorisation a github
     * @Route("/github_authorize", name="github_authorize")
     * @param Request $request
     * @return Response
     */
    public function githubgetauthorize(Request $request): Response
    {
        return $this->accessTokenProcess->githubgetauthorize($request);
    }

    /**
     * Processus Authorization Code Grant
     * @Route("/getaccesstokengithub", name="getaccesstokengithub")
     *
     * @param Request $request
     * @return JsonResponse
     * @throws IdentityProviderException
     */
    public function getaccesstokenGithub(Request $request): JsonResponse
    {
        return $this->accessTokenProcess->getaccesstokenGithub($request);
    }

    /**
     * Demande d'autorisation a google
     * @Route("/google_authorize", name="google_authorize")
     * @param Request $request
     * @return Response
     */
    public function googlegetauthorize(Request $request): Response
    {
        return $this->accessTokenProcess->googlegetauthorize($request);
    }

    /**
     * Processus Authorization Code Grant
     * @Route("/getaccesstokengoogle", name="getaccesstokengoogle")
     * @param Request $request
     * @return JsonResponse
     * @throws IdentityProviderException
     */
    public function getaccesstokenGoogle(Request $request): JsonResponse
    {
       return $this->accessTokenProcess->getaccesstokenGoogle($request);
    }

}