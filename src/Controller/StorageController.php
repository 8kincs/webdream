<?php /** @noinspection PhpUnused */

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\StorageRepository;
use App\Service\StorageService;
use App\Service\Utils;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/storage', name: 'storages_', methods: ['POST'])]
class StorageController extends AbstractController
{
    public function __construct(
        private StorageRepository $storageRepository,
        private ProductRepository $productRepository,
        private Utils $utils,
        private StorageService $storageService,
    ){}

    #[Route('/list', name: 'list')]
    public function listStorages(): Response
    {
        return $this->render('storage/list.html.twig', [
            'storages' => $this->storageRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/details', name: 'details')]
    public function showStorageDetails(Request $request): Response
    {
        $id = $request->request->get('id');

        return $this->render('storage/details.html.twig', [
            'storage' => $this->storageRepository->find($id),
        ]);
    }

    #[Route('/new', name: 'new')]
    public function newStorage(): Response
    {
        return $this->render('storage/new.html.twig');
    }

    #[Route('/add-product', name: 'add_product')]
    public function addProduct(): Response
    {
        return $this->render('storage/add-product.html.twig', [
            'storages' => $this->storageRepository->findBy([], ['name' => 'ASC']),
            'products' => $this->productRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    #[NoReturn]
    #[Route('/do-add-product', name: 'do_add_product')]
    public function doAddProduct(Request $request): Response
    {
        $data = $this->utils->serializedArrayToArray($request->request->all());

        $this->storageService->changeProductQuantityInStorage($data);

        /** @noinspection PhpUnreachableStatementInspection */
        return new Response('Sikeres termékmozgatás');
    }

    /**
     * @throws JsonException
     */
    #[Route('/save', name: 'save')]
    public function saveStorage(Request $request): Response
    {
        $data = $this->utils->serializedArrayToArray($request->request->all());

        $this->storageService->saveNewStorage($data);

        return new Response('Sikeres mentés');
    }
}
