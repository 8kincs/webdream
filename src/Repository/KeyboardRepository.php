<?php

namespace App\Repository;

use App\Entity\Keyboard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Keyboard|null find($id, $lockMode = null, $lockVersion = null)
 * @method Keyboard|null findOneBy(array $criteria, array $orderBy = null)
 * @method Keyboard[]    findAll()
 * @method Keyboard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KeyboardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Keyboard::class);
    }
}
