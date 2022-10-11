<?php

namespace Marmits\GoogleIdentification\Controller;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;


class UserController  extends AbstractController
{



    protected RequestStack $requestStack;

    /**
     * @param ContainerInterface $container
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $em

     */
    public function __construct(ContainerInterface $container, RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->container = $container;
        $this->requestStack = $requestStack;
        $this->em = $em;

    }

    /**
     *
     * @Route("/isvaliduser", name="isvaliduser",options={"expose"=true}, methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getIsValidUSer(Request $request): JsonResponse
    {

        return new jsonResponse(['code'=> 200, 'message' => 'Authorized User'], 200);

        //return new jsonResponse(['code'=> 401, 'message' => 'Unauthorized User'], 200);
      //  return new jsonResponse(['code'=> 403, 'message' => 'Forbidden User'], 200);

    }



}