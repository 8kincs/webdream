<?php

namespace App\DataFixtures;

use App\Entity\InStock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class InStockFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $inStocks = [
            ['0', 'kbd-101-hu', 300, 150],
            ['0', 'kbd-101-en', 300, 100],
            ['0', 'mon-2021/1', 200, 30],
            ['0', 'mon-2021/2', 200, 40],
            ['0', 'ssd-2021/1', 400, 200],
            ['0', 'ssd-2021/2', 400, 300],

            ['1', 'kbd-101-hu', 100, 10],
            ['1', 'kbd-101-en', 100, 20],
            ['1', 'mon-2021/1', 100, 5],
            ['1', 'mon-2021/2', 100, 8],
            ['1', 'ssd-2021/1', 100, 30],
            ['1', 'ssd-2021/2', 100, 30],

            ['2', 'kbd-101-hu', 30, 10],
            ['2', 'kbd-101-en', 30, 10],
            ['2', 'mon-2021/1', 20, 2],
            ['2', 'mon-2021/2', 20, 1],
            ['2', 'ssd-2021/1', 30, 10],
            ['2', 'ssd-2021/2', 30, 20],
        ];

        foreach ($inStocks as $inStock) {
            [$storageIndex, $articleNumber, $capacity, $stock] = $inStock;

            $inStockObject = new InStock();
            /** @noinspection PhpParamsInspection */
            $inStockObject
                ->setCapacity($capacity)
                ->setStock($stock)
                ->setStorage($this->getReference('store_' . $storageIndex))
                ->setProduct($this->getReference('prod_' . $articleNumber))
            ;
            $manager->persist($inStockObject);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            BrandFixtures::class,
            ProductFixtures::class,
            StorageFixtures::class,
        ];
    }
}
