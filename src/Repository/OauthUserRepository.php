<?php
declare(strict_types=1);

namespace Marmits\Oauth2Identification\Repository;

use Marmits\Oauth2Identification\Entity\OauthUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OauthUser>
 *
 * @method OauthUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method OauthUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method OauthUser[]    findAll()
 * @method OauthUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OauthUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OauthUser::class);
    }

    /**
     * @param OauthUser $entity
     * @param bool $flush
     * @return int
     */
    public function add(OauthUser $entity, bool $flush = false): int
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();

        }
        return $entity->getId();
    }

    public function remove(OauthUser $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return OauthUser[] Returns an array of OauthUser objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?OauthUser
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
