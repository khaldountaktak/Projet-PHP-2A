<?php

namespace App\Repository;

use App\Entity\Billet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Member;
use App\Entity\Exposition;

/**
 * @extends ServiceEntityRepository<Billet>
 */
class BilletRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Billet::class);
    }
    public function save(Billet $entity, bool $flush=false){
        $this->getEntityManager()->persist($entity);

        if ($flush){
            $this->getEntityManager()->flush();
        }
    }

        /**
     * @return Billet[] Returns an array of Billet objects for a specific member
     */
    public function findMemberBillets(Member $member): array
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.album', 'i')
            ->andWhere('i.member = :member')
            ->setParameter('member', $member)
            ->getQuery()
            ->getResult();
    }

    public function remove(Billet $entity, bool $flush = false): void
    {
        $expositionRepository = $this->getEntityManager()->getRepository(Exposition::class);

        // Get rid of the ManyToMany relation with Expositions
        $expositions = $expositionRepository->findBilletExpositions($entity);   
        foreach ($expositions as $exposition) {
            $exposition->removeBillet($entity); // Assuming Exposition has a removeBillet() method
            $this->getEntityManager()->persist($exposition);
        }

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        // Finally, remove the Billet entity itself
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    //    /**
    //     * @return Billet[] Returns an array of Billet objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Billet
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
