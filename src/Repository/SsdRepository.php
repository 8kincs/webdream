<?php

namespace App\Repository;

use App\Entity\Ssd;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ssd|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ssd|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ssd[]    findAll()
 * @method Ssd[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SsdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ssd::class);
    }
}
