<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BrandFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $brands = [
            ['Asus', 4],
            ['Kingston', 5],
            ['LG', 3],
            ['Acer', 4],
            ['Logitech', 4],
            ['Chicony', 3],
        ];

        foreach ($brands as $brand) {
            [$name, $qualityCategory] = $brand;

            $brandObject = new Brand();
            $brandObject
                ->setName($name)
                ->setQualityCategory($qualityCategory)
            ;

            $manager->persist($brandObject);
            $this->addReference('brand_' . $name, $brandObject);
        }

        $manager->flush();
    }
}
