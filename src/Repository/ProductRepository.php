<?php /** @noinspection PhpUnusedPrivateFieldInspection */

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends BaseRepository
{
    private array $productProperties = [
        'monitor'   => ['size'],
        'keyboard'  => ['layout'],
        'ssd'       => ['capacity'],
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }
}
