<?php

namespace App\DataFixtures;

use App\Entity\Keyboard;
use App\Entity\Monitor;
use App\Entity\Product;
use App\Entity\Ssd;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $monitors = [
            ['mon-2021/1', 'LG 17"', 3523000, 'LG', 170],
            ['mon-2021/2', 'Acer 23.5"', 4898700, 'Acer', 235],
        ];

        $keyboards = [
            ['kbd-101-hu', '101 gombos Logitech magyar', 346200, 'Logitech', 'HU'],
            ['kbd-101-en', '101 gombos Chicony angol', 446200, 'Chicony', 'EN'],
        ];

        $ssds = [
            ['ssd-2021/1', '512G Kingstone', 2480000, 'Kingston', 512],
            ['ssd-2021/2', '1T Asus', 3680000, 'Asus', 1024],
        ];

        foreach ($monitors as $monitor) {
            [$articleNumber, $name, $price, $brandName, $size] = $monitor;

            $productObject = $this->newProduct($manager, [$articleNumber, $name, $price, $brandName, Monitor::class]);

            $monitorObject = new Monitor();
            $monitorObject
                ->setSize($size)
                ->setProduct($productObject)
            ;
            $manager->persist($monitorObject);
            $this->addReference('prod_' . $articleNumber, $productObject);
        }

        foreach ($keyboards as $keyboard) {
            [$articleNumber, $name, $price, $brandName, $layout] = $keyboard;

            $productObject = $this->newProduct($manager, [$articleNumber, $name, $price, $brandName, Keyboard::class]);

            $keyboardObject = new Keyboard();
            $keyboardObject
                ->setLayout($layout)
                ->setProduct($productObject)
            ;
            $manager->persist($keyboardObject);
            $this->addReference('prod_' . $articleNumber, $productObject);
        }

        foreach ($ssds as $ssd) {
            [$articleNumber, $name, $price, $brandName, $capacity] = $ssd;

            $productObject = $this->newProduct($manager, [$articleNumber, $name, $price, $brandName, Ssd::class]);

            $ssdObject = new Ssd();
            $ssdObject
                ->setCapacity($capacity)
                ->setProduct($productObject)
            ;
            $manager->persist($ssdObject);
            $this->addReference('prod_' . $articleNumber, $productObject);
        }

        $manager->flush();
    }

    private function newProduct($manager, $pars): Product
    {
        [$articleNumber, $name, $price, $brandName, $className] = $pars;

        $productObject = new Product();
        /** @noinspection PhpParamsInspection */
        $productObject
            ->setArticleNumber($articleNumber)
            ->setName($name)
            ->setPrice($price)
            ->setClassName($className)
            ->setBrand($this->getReference('brand_' . $brandName))
        ;
        $manager->persist($productObject);
        $manager->flush();

        return $productObject;
    }

    public function getDependencies(): array
    {
        return [
            BrandFixtures::class,
        ];
    }
}
