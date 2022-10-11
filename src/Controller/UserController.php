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
use Marmits\GoogleIdentification\Entity\Datas;
use Marmits\GoogleIdentification\Repository\DatasRepository;


class UserController  extends AbstractController
{



    protected RequestStack $requestStack;
    protected EntityManagerInterface $em;
    protected DatasRepository $repository;

    /**
     * @param ContainerInterface $container
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $em
     * @param DatasRepository $repository

     */
    public function __construct(ContainerInterface $container, RequestStack $requestStack, EntityManagerInterface $em, DatasRepository $repository)
    {
        $this->container = $container;
        $this->requestStack = $requestStack;
        $this->em = $em;
        $this->repository = $repository;

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

        $datas = $this->repository->findAll();
        $count = count($datas);
        if($count > 0) {
            return new jsonResponse(['code' => 200, 'message' => 'Authorized User', 'nbdatas' => $count], 200);
        }




        return new jsonResponse(['code'=> 401, 'message' => 'Unauthorized User'], 200);
      //  return new jsonResponse(['code'=> 403, 'message' => 'Forbidden User'], 200);

    }



}