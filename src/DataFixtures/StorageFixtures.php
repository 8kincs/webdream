<?php

namespace App\DataFixtures;

use App\Entity\Storage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StorageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $storages = [
            ['Nagy Raktárbázis', '1234', 'Budapest', 'Villányi út 12.' ],
            ['Közepes Raktár', '8800', 'Nagykanizsa', 'Attila u. 3.'],
            ['Kis Raktár', '4400', 'Nyíregyháza', 'Adakozó u. 37/B'],
        ];

        /* */
        $i = 0;
        foreach ($storages as $storage) {
            [$name, $postalCode, $city, $street] = $storage;

            $storageObject = new Storage();
            $storageObject
                ->setName($name)
                ->setPostalCode($postalCode)
                ->setCity($city)
                ->setStreet($street)
            ;

            $manager->persist($storageObject);
            $this->addReference('store_' . $i, $storageObject);

            $i++;
        }

        $manager->flush();
        /* */
    }
}
