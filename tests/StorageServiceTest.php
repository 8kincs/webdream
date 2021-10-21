<?php /** @noinspection PhpDeprecationInspection */

/** @noinspection PhpFieldAssignmentTypeMismatchInspection */

namespace App\Tests;

use App\Entity\InStock;
use App\Entity\Product;
use App\Entity\Storage;
use App\Repository\InStockRepository;
use App\Repository\ProductRepository;
use App\Repository\StorageRepository;
use App\Service\StorageService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StorageServiceTest extends KernelTestCase
{
    private const ADD_PRODUCTS_1 = 38;
    private const ADD_PRODUCTS_2 = 20;
    private const ADD_PRODUCTS_3 = 20;
    private const ADD_PRODUCTS_4 = 40;

    private const GET_PRODUCTS_1 = -38;
    private const GET_PRODUCTS_2 = -20;
    private const GET_PRODUCTS_3 = -20;
    private const GET_PRODUCTS_4 = -40;

    private ?EntityManager $em;

    // JSON encoded array of newly generated InStock records.
    private static ?string $newInStockRecords;

    private static ?StorageRepository $storageRepository;

    private static ?InStockRepository $inStockRepository;

    private static StorageService $storageService;

    private static Storage $storage0;

    private static Storage $storage1;

    private static Product $testProduct;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->em = $container
            ->get('doctrine')
            ->getManager();

        self::$storageService = $container->get(StorageService::class);

        self::$inStockRepository = $container->get(InStockRepository::class);

        self::$storageRepository = $container->get(StorageRepository::class);

        /** @var ProductRepository $productRepository */
        $productRepository1 = $container->get(ProductRepository::class);
        $products = $productRepository1->findAll();

        self::$testProduct = $products[0];

        /**
         * Defaults for products:
         *  - capacity = StorageService::DEFAULT_STORAGE_CAPACITY (50)
         *  - stock = 0
         */
        $newInStockRecords = [];
        foreach ($products as $product) {
            $newInStockRecords[] = [$product->getId(), StorageService::DEFAULT_STORAGE_CAPACITY, 0];
        }
        self::$newInStockRecords = json_encode($newInStockRecords);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testBaseState()
    {
        // Make base state of database: no storages, no inStocks
        $this->clearTables();

        $this->assertTrue(true);
    }

    /**
     * @depends testBaseState
     * @dataProvider providerStorages
     *
     * @throws ORMException
     */
    public function testCreateStorage($data): void
    {
        (self::$storageService)->saveNewStorage($data);

        $this->checkLastStorageRecord($data);
    }

    /**
     * @depends testCreateStorage
     */
    public function testNumberOfStorages(): void
    {
        $this->assertCount(2, self::$storageRepository->findAll());

        $storages = self::$storageRepository->findAll();
        self::$storage0 = $storages[0];
        self::$storage1 = $storages[1];
    }

    /**
     * 2 storages created.
     *
     * @depends testNumberOfStorages
     *
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpUnreachableStatementInspection
     */
    #[NoReturn] public function testAddingProductsToStorage0(): void
    {
        $product = self::$testProduct;

        // ADD_PRODUCTS_1 (38)
        /** @var StorageService $storageService */
        (self::$storageService)->changeProductQuantityInStorage([
            'storageId' => self::$storage0->getId(),
            'productId' => $product->getId(),
            'quantity' => self::ADD_PRODUCTS_1,
        ]);
        $this->check1AddingProductToStorage0($product);

        // Add +20 products (ADD_PRODUCTS_2) - there will be not enough room (capacity = 50), must be used the other storage.
        self::$storageService->changeProductQuantityInStorage([
            'storageId' => self::$storage0->getId(),
            'productId' => $product->getId(),
            'quantity' => self::ADD_PRODUCTS_2,
        ]);
        $this->check2AddingProductToStorage0($product);

        // Add +20 products (ADD_PRODUCTS_3) to $storage (which is now full) - this must be added to other storage.
        self::$storageService->changeProductQuantityInStorage([
            'storageId' => self::$storage0->getId(),
            'productId' => $product->getId(),
            'quantity' => self::ADD_PRODUCTS_3,
        ]);
        $this->check3AddingProductToStorage0($product);

        /**
         * Add +40 products (ADD_PRODUCTS_4) to $storage (which is now full) -
         *  this must be added to other storage, and this will be full as well.
         * All the storages are full. Must be an exception.
         */
        $leftovers =
            self::ADD_PRODUCTS_1 +
            self::ADD_PRODUCTS_2 +
            self::ADD_PRODUCTS_3 +
            self::ADD_PRODUCTS_4 -
            2 * StorageService::DEFAULT_STORAGE_CAPACITY
        ;
        $this->expectExceptionMessage('Nem sikerült a kérést teljes egészében teljesíteni. Maradék: ' . $leftovers);
        self::$storageService->changeProductQuantityInStorage([
            'storageId' => self::$storage0->getId(),
            'productId' => $product->getId(),
            'quantity' => self::ADD_PRODUCTS_4,
        ]);
    }

    /**
     * 2 storages created. In each of them self:$testProduct on full capacity.
     *
     * @depends testAddingProductsToStorage0
     *
     * @throws Exception
     * @noinspection PhpUnreachableStatementInspection
     */
    #[NoReturn]
    public function testGettingProductFromStorage0(): void
    {
        $product = self::$testProduct;

        // GET_PRODUCTS_1 (38)
        /** @var StorageService $storageService */
        (self::$storageService)->changeProductQuantityInStorage([
            'storageId' => self::$storage0->getId(),
            'productId' => $product->getId(),
            'quantity' => self::GET_PRODUCTS_1,
        ]);
        $this->check1GettingProductToStorage0($product);

        /* */
        // Get +20 products (GET_PRODUCTS_2) - there will be not enough stock, must be used the other storage.
        self::$storageService->changeProductQuantityInStorage([
            'storageId' => self::$storage0->getId(),
            'productId' => $product->getId(),
            'quantity' => self::GET_PRODUCTS_2,
        ]);
        $this->check2GettingProductToStorage0($product);

        // Get +20 products (GET_PRODUCTS_3) from $storage (which is now empty) - this must be get from other storage.
        self::$storageService->changeProductQuantityInStorage([
            'storageId' => self::$storage0->getId(),
            'productId' => $product->getId(),
            'quantity' => self::GET_PRODUCTS_3,
        ]);
        $this->check3GettingProductToStorage0($product);

        /**
         * Get +40 products (GET_PRODUCTS_4) from $storage (which is now empty) -
         *  this must be get from other storage, and this will be empty as well.
         * All the storages are empty. Must be an exception.
         */
        $leftovers =
            self::GET_PRODUCTS_1 +
            self::GET_PRODUCTS_2 +
            self::GET_PRODUCTS_3 +
            self::GET_PRODUCTS_4 +
            2 * StorageService::DEFAULT_STORAGE_CAPACITY
        ;
        $this->expectExceptionMessage('Nem sikerült a kérést teljes egészében teljesíteni. Maradék: ' . $leftovers);
        self::$storageService->changeProductQuantityInStorage([
            'storageId' => self::$storage0->getId(),
            'productId' => $product->getId(),
            'quantity' => self::GET_PRODUCTS_4,
        ]);
    }

    /**
     * End of testing: Remove generated storage and inStock records from database.
     *
     * @depends testGettingProductFromStorage0
     */
    public function testClearDatabase()
    {
        $this->clearTables();

        $this->assertTrue(true);
    }

    public function providerStorages(): array
    {
        return [
            [[
                'name' => 'Délnyugati Raktár',
                'postalCode' => '8900',
                'city' => 'Zalaegerszeg',
                'street' => 'Újlaki krt. 123.',
            ]],
            [[
                'name' => 'Zala #2 Raktár',
                'postalCode' => '8800',
                'city' => 'Nagykanizsa',
                'street' => 'Attila u. 12.',
            ]],
        ];
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws ORMException
     */
    private function checkLastStorageRecord(array $data)
    {
        $storage = self::$storageRepository->findLast();
        $this->em->refresh($storage);

        $savedData = [
            'name' => $storage->getName(),
            'postalCode' => $storage->getPostalCode(),
            'city' => $storage->getCity(),
            'street' => $storage->getStreet(),
        ];

        // Check storage record.
        $this->assertJsonStringEqualsJsonString(json_encode($data), json_encode($savedData));

        // Check inStock records.
        $inStockSaved = [];
        $inStockRecords = $storage->getInStocks();

        /** @var InStock $inStock */
        foreach ($inStockRecords as $inStock) {
            $inStockSaved[] = [
                $inStock->getProduct()->getId(),
                $inStock->getCapacity(),
                $inStock->getStock(),
            ];
        }
        $this->assertJsonStringEqualsJsonString(self::$newInStockRecords, json_encode($inStockSaved));
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Doing this is recommended to avoid memory leaks.
        $this->em->close();
        $this->em = null;

        self::$storageRepository = null;
        self::$newInStockRecords = null;
    }

    private function check1AddingProductToStorage0($product)
    {
        // Check changes in database.
        /** @noinspection PhpUnreachableStatementInspection */
        /** @var InStock $inStockChanged */
        $inStockChanged = self::$inStockRepository->findOneBy([
            'storage' => self::$storage0,
            'product' => $product,
        ]);
        $inStockChangedId = $inStockChanged->getId();

        $this->assertEquals(StorageService::DEFAULT_STORAGE_CAPACITY, $inStockChanged->getCapacity());
        $this->assertEquals(self::ADD_PRODUCTS_1, $inStockChanged->getStock());

        // Check that other InStock records did not change.
        $inStocks = self::$inStockRepository->findAll();
        foreach ($inStocks as $inStock) {
            if ($inStock->getId() !== $inStockChangedId) {
                $this->assertEquals(StorageService::DEFAULT_STORAGE_CAPACITY, $inStock->getCapacity());
                $this->assertEquals(0, $inStock->getStock());
            }
        }
    }

    /**
     * @throws ORMException
     */
    private function check2AddingProductToStorage0($product)
    {
        // Check $storage0 - must be full
        /** @noinspection PhpUnreachableStatementInspection */
        /** @var InStock $inStockChanged0 */
        $inStockChanged0 = self::$inStockRepository->findOneBy([
            'storage' => self::$storage0,
            'product' => $product,
        ]);
        $this->em->refresh($inStockChanged0);
        $inStockChanged0Id = $inStockChanged0->getId();

        $this->assertEquals(StorageService::DEFAULT_STORAGE_CAPACITY, $inStockChanged0->getCapacity());
        $this->assertEquals(
            StorageService::DEFAULT_STORAGE_CAPACITY,
            $inStockChanged0->getStock()
        );

        // Check $storage1 - must be (ADD_PRODUCTS_1 + ADD_PRODUCTS_2 - capacity)
        /** @noinspection PhpUnreachableStatementInspection */
        /** @var InStock $inStockChanged0 */
        $inStockChanged1 = self::$inStockRepository->findOneBy([
            'storage' => self::$storage1,
            'product' => $product,
        ]);
        $this->em->refresh($inStockChanged1);
        $inStockChanged1Id = $inStockChanged1->getId();

        $this->assertEquals(StorageService::DEFAULT_STORAGE_CAPACITY, $inStockChanged1->getCapacity());
        $this->assertEquals(
            self::ADD_PRODUCTS_1 + self::ADD_PRODUCTS_2 - StorageService::DEFAULT_STORAGE_CAPACITY,
            $inStockChanged1->getStock()
        );

        // Check that other InStock records did not change.
        $inStocks = self::$inStockRepository->findAll();
        foreach ($inStocks as $inStock) {
            $this->em->refresh($inStock);
            if ($inStock->getId() !== $inStockChanged0Id && $inStock->getId() !== $inStockChanged1Id) {
                $this->assertEquals(StorageService::DEFAULT_STORAGE_CAPACITY, $inStock->getCapacity());
                $this->assertEquals(0, $inStock->getStock());
            }
        }
    }

    /**
     * @throws ORMException
     */
    private function check3AddingProductToStorage0($product)
    {
        // Check $storage0 - must be full
        /** @noinspection PhpUnreachableStatementInspection */
        /** @var InStock $inStockChanged0 */
        $inStockChanged0 = self::$inStockRepository->findOneBy([
            'storage' => self::$storage0,
            'product' => $product,
        ]);
        $this->em->refresh($inStockChanged0);
        $inStockChanged0Id = $inStockChanged0->getId();

        $this->assertEquals(StorageService::DEFAULT_STORAGE_CAPACITY, $inStockChanged0->getCapacity());
        $this->assertEquals(
            StorageService::DEFAULT_STORAGE_CAPACITY,
            $inStockChanged0->getStock()
        );

        // Check $storage1 - must be (ADD_PRODUCTS_1 + ADD_PRODUCTS_2 + ADD_PRODUCTS_3 - capacity)
        /** @noinspection PhpUnreachableStatementInspection */
        /** @var InStock $inStockChanged0 */
        $inStockChanged1 = self::$inStockRepository->findOneBy([
            'storage' => self::$storage1,
            'product' => $product,
        ]);
        $this->em->refresh($inStockChanged1);
        $inStockChanged1Id = $inStockChanged1->getId();

        $this->assertEquals(StorageService::DEFAULT_STORAGE_CAPACITY, $inStockChanged1->getCapacity());
        $this->assertEquals(
            self::ADD_PRODUCTS_1 + self::ADD_PRODUCTS_2 + self::ADD_PRODUCTS_3 - StorageService::DEFAULT_STORAGE_CAPACITY,
            $inStockChanged1->getStock()
        );

        // Check that other InStock records did not change.
        $inStocks = self::$inStockRepository->findAll();
        foreach ($inStocks as $inStock) {
            $this->em->refresh($inStock);
            if ($inStock->getId() !== $inStockChanged0Id && $inStock->getId() !== $inStockChanged1Id) {
                $this->assertEquals(StorageService::DEFAULT_STORAGE_CAPACITY, $inStock->getCapacity());
                $this->assertEquals(0, $inStock->getStock());
            }
        }
    }

    private function check1GettingProductToStorage0($product)
    {
        // Check changes in database.
        /** @noinspection PhpUnreachableStatementInspection */
        /** @var InStock $inStockChanged */
        $inStockChanged = self::$inStockRepository->findOneBy([
            'storage' => self::$storage0,
            'product' => $product,
        ]);
        $inStockChangedId = $inStockChanged->getId();

        // Same product in other storage
        /** @var InStock $inStockOfOtherStorage */
        $inStockOfOtherStorage = self::$inStockRepository->findOneBy([
            'storage' => self::$storage1,
            'product' => $product,
        ]);
        $inStockOfOtherStorageId = $inStockOfOtherStorage->getId();

        $this->assertEquals(StorageService::DEFAULT_STORAGE_CAPACITY, $inStockChanged->getCapacity());
        $this->assertEquals(StorageService::DEFAULT_STORAGE_CAPACITY + self::GET_PRODUCTS_1, $inStockChanged->getStock());

        // Check that other InStock records did not change.
        $inStocks = self::$inStockRepository->findAll();
        foreach ($inStocks as $inStock) {
            if ($inStock->getId() !== $inStockChangedId && $inStock->getId() !== $inStockOfOtherStorageId) {
                $this->assertEquals(StorageService::DEFAULT_STORAGE_CAPACITY, $inStock->getCapacity());
                $this->assertEquals(0, $inStock->getStock());
            }
        }
    }

    /**
     * @throws ORMException
     */
    private function check2GettingProductToStorage0($product)
    {
        // Check $storage0 - must be empty
        /** @noinspection PhpUnreachableStatementInspection */
        /** @var InStock $inStockChanged0 */
        $inStockChanged0 = self::$inStockRepository->findOneBy([
            'storage' => self::$storage0,
            'product' => $product,
        ]);
        $this->em->refresh($inStockChanged0);
        $inStockChanged0Id = $inStockChanged0->getId();

        $this->assertEquals(StorageService::DEFAULT_STORAGE_CAPACITY, $inStockChanged0->getCapacity());
        $this->assertEquals(
            0,
            $inStockChanged0->getStock()
        );

        // Check $storage1 - must be (2 * capacity + GET_PRODUCTS_1 + GET_PRODUCTS_2 - )
        /** @noinspection PhpUnreachableStatementInspection */
        /** @var InStock $inStockChanged0 */
        $inStockChanged1 = self::$inStockRepository->findOneBy([
            'storage' => self::$storage1,
            'product' => $product,
        ]);
        $this->em->refresh($inStockChanged1);
        $inStockChanged1Id = $inStockChanged1->getId();

        $this->assertEquals(StorageService::DEFAULT_STORAGE_CAPACITY, $inStockChanged1->getCapacity());
        $this->assertEquals(
            self::GET_PRODUCTS_1 + self::GET_PRODUCTS_2 + 2 * StorageService::DEFAULT_STORAGE_CAPACITY,
            $inStockChanged1->getStock()
        );

        // Check that other InStock records did not change.
        $inStocks = self::$inStockRepository->findAll();
        foreach ($inStocks as $inStock) {
            $this->em->refresh($inStock);
            if ($inStock->getId() !== $inStockChanged0Id && $inStock->getId() !== $inStockChanged1Id) {
                $this->assertEquals(StorageService::DEFAULT_STORAGE_CAPACITY, $inStock->getCapacity());
                $this->assertEquals(0, $inStock->getStock());
            }
        }
    }

    /**
     * @throws ORMException
     */
    private function check3GettingProductToStorage0($product)
    {
        // Check $storage0 - must be empty
        /** @noinspection PhpUnreachableStatementInspection */
        /** @var InStock $inStockChanged0 */
        $inStockChanged0 = self::$inStockRepository->findOneBy([
            'storage' => self::$storage0,
            'product' => $product,
        ]);
        $this->em->refresh($inStockChanged0);
        $inStockChanged0Id = $inStockChanged0->getId();

        $this->assertEquals(StorageService::DEFAULT_STORAGE_CAPACITY, $inStockChanged0->getCapacity());
        $this->assertEquals(
            0,
            $inStockChanged0->getStock()
        );

        // Check $storage1 - must be (GET_PRODUCTS_1 + GET_PRODUCTS_2 + GET_PRODUCTS_3 + 2 * capacity)
        /** @noinspection PhpUnreachableStatementInspection */
        /** @var InStock $inStockChanged0 */
        $inStockChanged1 = self::$inStockRepository->findOneBy([
            'storage' => self::$storage1,
            'product' => $product,
        ]);
        $this->em->refresh($inStockChanged1);
        $inStockChanged1Id = $inStockChanged1->getId();

        $this->assertEquals(StorageService::DEFAULT_STORAGE_CAPACITY, $inStockChanged1->getCapacity());
        $this->assertEquals(
            self::GET_PRODUCTS_1 + self::GET_PRODUCTS_2 + self::GET_PRODUCTS_3 + 2 * StorageService::DEFAULT_STORAGE_CAPACITY,
            $inStockChanged1->getStock()
        );

        // Check that other InStock records did not change.
        $inStocks = self::$inStockRepository->findAll();
        foreach ($inStocks as $inStock) {
            $this->em->refresh($inStock);
            if ($inStock->getId() !== $inStockChanged0Id && $inStock->getId() !== $inStockChanged1Id) {
                $this->assertEquals(StorageService::DEFAULT_STORAGE_CAPACITY, $inStock->getCapacity());
                $this->assertEquals(0, $inStock->getStock());
            }
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function clearTables()
    {
        $inStocks = self::$inStockRepository->findAll();
        foreach ($inStocks as $inStock) {
            $this->em->remove($inStock);
        }
        $this->em->flush();

        $storages = self::$storageRepository->findAll();
        foreach ($storages as $storage) {
            $this->em->remove($storage);
        }
        $this->em->flush();
    }
}
