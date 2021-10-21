<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Storage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

abstract class BaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findLast(): Storage|Product
    {
        return $this->createQueryBuilder('x')
            ->orderBy('x.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult()
            ;
    }
}
