<?php

namespace App\Repository;

use App\Entity\Media;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Media>
 *
 * @method Media|null find(mixed $id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Media|null findOneBy(array<string, mixed> $criteria, ?array<string, string> $orderBy = null)
 * @method list<Media> findAll()
 * @method list<Media> findBy(array<string, mixed> $criteria, ?array<string, string> $orderBy = null, ?int $limit = null, ?int $offset = null)
 */
class MediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }

    /**
    * @return list<Media>
    */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.user = :user')
            ->setParameter('user', $user)
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Media[] Returns an array of Media objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Media
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
