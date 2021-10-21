<?php

namespace App\Repository;

use App\Entity\Storage;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Storage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Storage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Storage[]    findAll()
 * @method Storage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StorageRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Storage::class);
    }
}
