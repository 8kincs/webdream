<?php

namespace App\Service;

use App\Entity\InStock;
use App\Entity\Storage;
use App\Repository\InStockRepository;
use App\Repository\ProductRepository;
use App\Repository\StorageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JetBrains\PhpStorm\NoReturn;

class StorageService
{
    public const DEFAULT_STORAGE_CAPACITY = 50;

    public function __construct(
        private EntityManagerInterface $em,
        private ProductRepository $productRepository,
        private StorageRepository $storageRepository,
        private InStockRepository $inStockRepository,
    ) {
    }

    /**
     * @throws Exception
     */
    #[NoReturn]
    public function changeProductQuantityInStorage(array $data)
    {
        [$storageId, $productId, $quantity] = array_values($data);

        $storage = $this->storageRepository->find($storageId);
        $product = $this->productRepository->find($productId);

        $inStock = $this->inStockRepository->findOneBy([
            'storage' => $storage,
            'product' => $product,
        ]);
        $leftovers = $inStock->changeProductQuantity($quantity);

        $this->em->persist($inStock);
        $this->em->flush();

        if ($leftovers === 0) {
            // Nothing left to do.
            return;
        }

        // We try to complete request by other storages.
        $storages = $this->storageRepository->findAll();
        foreach ($storages as $warehouse) {
            $inStock = $this->inStockRepository->findOneBy([
                'storage' => $warehouse,
                'product' => $product,
            ]);
            $leftovers = $inStock->changeProductQuantity($leftovers);

            $this->em->persist($inStock);

            if ($leftovers === 0) {
                break;
            }
        }
        $this->em->flush();

        if ($leftovers !== 0) {
            throw new Exception('Nem sikerült a kérést teljes egészében teljesíteni. Maradék: ' . $leftovers);
        }
    }

    public function saveNewStorage(array $data): void
    {
        [$name, $postalCode, $city, $street] = array_values($data);

        $newStorage = new Storage();
        $newStorage
            ->setName($name)
            ->setPostalCode($postalCode)
            ->setCity($city)
            ->setStreet($street)
        ;

        $this->em->persist($newStorage);
        $this->em->flush();

        // Giv every product to the new storage with default storage capacity and 0 stock.
        $products = $this->productRepository->findAll();
        foreach ($products as $product) {
            $inStockObject = new InStock();
            $inStockObject
                ->setStorage($newStorage)
                ->setProduct($product)
                ->setCapacity(self::DEFAULT_STORAGE_CAPACITY)
                ->setStock(0)
            ;
            $this->em->persist($inStockObject);
        }
        $this->em->flush();
    }
}