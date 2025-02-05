<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    public function save(Commande $commande, bool $flush = false): void
    {
        $this->getEntityManager()->persist($commande);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Commande $commande, bool $flush = false): void
    {
        $this->getEntityManager()->remove($commande);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByUserId(string $userId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.userId = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('c.dateCommande', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
