<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findAllWithMedias(): array
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.medias', 'm')
            ->addSelect('m')
            ->getQuery()
            ->getResult();
    }
}





// https://afsy.fr/avent/2013/14-votre-application-est-lente-pensez-a-optimiser-doctrine
        //     $qb = $this
        //     ->createQueryBuilder('user')
        //     ->addSelect('group')
        //     ->leftJoin('user.groups', 'group')
        //     ->where(...)
        // ;

        // return $qb->getQuery()->execute();

// REQUETES NATIVES
//         // Get connection
// $conn = $entityManager->getConnection();

// // Get table name
// $meta = $entityManager->getClassMetadata(User::class);
// $tableName = $meta->getTableName();

// // Get random ids
// $sql = "SELECT id AS id FROM $tableName WHERE active = true ORDER BY RAND()";
// $statement = $conn->executeQuery($sql);
// $fetchedIds = array_map(function ($element) {
//     return $element['id'];
// }, $statement->fetchAll());

// return $fetchedIds;



    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

