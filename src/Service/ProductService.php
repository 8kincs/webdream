<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductService
{
    public function __construct(
        private EntityManagerInterface $em,
    ){
    }

    public function getProductProperties(Product $product, bool $formatted = true): string
    {
        $className = $product->getClassName();
        $realProduct = $this->em
            ->getRepository($className)
            ->findOneBy(['product' => $product])
        ;

        switch ($className) {
            case 'App\Entity\Keyboard':
                $orig = $realProduct->getLayout();
                $toShow = $orig;
                break;

            /* */
            case 'App\Entity\Monitor':
                $orig = $realProduct->getSize();
                $toShow = ($orig / 10) . '"';
                break;

            case 'App\Entity\Ssd':
                $orig = $realProduct->getCapacity();
                $toShow = $orig . 'G';
                break;

            default:
                $orig = '?';
                $toShow = $orig;
        }

        return $formatted ? $toShow : $orig;
    }
}