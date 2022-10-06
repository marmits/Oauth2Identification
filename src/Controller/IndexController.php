<?php

namespace Marmits\GoogleIdentification\Controller;

use Exception;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use Symfony\Component\ErrorHandler\Error\ClassNotFoundError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Marmits\GoogleIdentification\Services\GoogleProvider;


/**
 *
 */
class IndexController extends AbstractController
{

    protected GoogleProvider $googleProvider;
    protected SessionInterface $session;

    /**
     * @param ContainerInterface $container
     * @param GoogleProvider $googleProvider
     */
    public function __construct(ContainerInterface $container, SessionInterface $session, GoogleProvider $googleProvider)
    {
        $this->container = $container;
        $this->session = $session;
        $this->googleProvider = $googleProvider;
    }

    /**
     *
     * @Route("/bundle_index", name="bundle_index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {

        return $this->render('@MarmitsGoogleIdentification/default.html.twig', [
            'googleProvider' => $this->googleProvider->getParams()
        ]);
    }

    /**
     *
     * @Route("/getaccesstoken", name="getaccesstoken")
     *
     * @param Request $request
     * @return JsonResponse
     * @throws IdentityProviderException
     */
    public function getaccesstoken(Request $request): JsonResponse
    {
        $code = null;
        $error = null;
        $state = null;
        $id_token = -1;
        if ($request->get('code')) {
            $code = $request->get('code');
        }
        if ($request->get('error')) {
            $error = $request->get('error');
        }
        if ($request->get('state')) {
            $state = $request->get('state');
        }

        $authorizationUrl =  $this->googleProvider->getInstance()->getAuthorizationUrl();

        if ($error !== null) {

            // Got an error, probably user denied access
            exit('Got error: ' . htmlspecialchars( $error['error'], ENT_QUOTES, 'UTF-8'));

        } elseif ($code !== null) {

            // If we don't have an authorization code then get one
            $this->session->set('oauth2state', $this->googleProvider->getInstance()->getState());
            header('Location: ' . $authorizationUrl);
            exit;

        } elseif (($state === null) || ($this->session->has('oauth2state') && $state !== $this->session->get('oauth2state'))) {

            // State is invalid, possible CSRF attack in progress
            if ( $this->session->has('oauth2state')) {
                $this->session->remove('oauth2state');
            }

            return new jsonResponse(['message' => 'Invalid state'], 500);


        } else {

            // Try to get an access token (using the authorization code grant)
            $accessToken = $this->googleProvider->getInstance()->getAccessToken('authorization_code', [
                'code' => $code
            ]);

            // Optional: Now you have a token you can look up a users profile data
            try {

                // We got an access token, let's now get the owner details
                $ownerDetails = $this->googleProvider->getInstance()->getResourceOwner($accessToken);

                // Use these details to create a new profile
                if(isset($accessToken->getValues()['id_token'])){
                    $id_token = $accessToken->getValues()['id_token'];
                }

                $res = [
                    'accesstoken' => $accessToken->getToken(),
                    'refreshtoken' => $accessToken->getRefreshToken(),
                    'expiredin' => $accessToken->getExpires(),
                    'expired' => $accessToken->hasExpired() ,
                    'id_token'=> $id_token,
                    'firstName' => $ownerDetails->getFirstName()
                ];
                return new jsonResponse($res, 200);

            } catch (IdentityProviderException $e) {

                // Failed to get user details
                exit('Something went wrong: ' . $e->getMessage());

            }
        }
    }

}
