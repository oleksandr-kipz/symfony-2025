<?php

namespace App\Controller\Product;

use App\Entity\Product;
use App\Entity\User;
use App\Service\Product\ProductService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
final class ProductController extends AbstractController
{

    private const CREATE_PRODUCT_DATA = [
        "name",
        "description",
        "price"
    ];

    /**
     * @param EntityManagerInterface $entityManager
     * @param ProductService $productService
     * @param RequestCheckerService $requestCheckerService
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductService         $productService,
        private readonly RequestCheckerService  $requestCheckerService
    ) {}

    #[Route('/products', name: 'app_get_products', methods: ['GET'])]
    public function getProducts(Request $request): JsonResponse
    {
        $queryParams = $request->query->all();

        $itemsPerPage = $queryParams['itemsPerPage'] ?? 5;
        $page = $queryParams['page'] ?? 1;

        $products = $this->entityManager->getRepository(Product::class)->getProducts($queryParams, $itemsPerPage, $page);

        return $this->json($products);
    }

    /**
     * @throws Exception
     */
    #[Route('/products', name: 'app_post_products', methods: ['POST'])]
    #[IsGranted("ROLE_USER")]
    public function createProduct(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $this->requestCheckerService->check($requestData, self::CREATE_PRODUCT_DATA);

        $product = $this->productService->createProduct($requestData);

        /** @var User $user */
        $user = $this->getUser();

        $product->setUser($user);

        $this->requestCheckerService->validateRequestDataByConstraints($product);

        $this->entityManager->flush();

        return $this->json($product);
    }

    #[Route('/products/{id}', name: 'app_get_products_item', methods: ['GET'])]
    public function getProductById(string $id): JsonResponse
    {
        /** @var Product $product */
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);

        return $this->json($product);
    }

    #[Route('/products/{id}', name: 'app_delete_products_item', methods: ['DELETE'])]
    #[IsGranted("ROLE_USER")]
    public function deleteProduct(string $id): JsonResponse
    {
        /** @var Product $product */
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);

        if (!$product) {
            throw new NotFoundHttpException('Product by id ' . $id . ' not found');
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->json([], 204);
    }

    #[Route('/products/{id}', name: 'app_patch_products_item', methods: ['PATCH'])]
    #[IsGranted("ROLE_USER")]
    public function updateProduct(string $id, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        /** @var Product $product */
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);

        if (!$product) {
            throw new NotFoundHttpException('Product by id ' . $id . ' not found');
        }

        if (isset($requestData['price'])) {
            $product->setPrice($requestData['price']);
        }

        if (isset($requestData['name'])) {
            $product->setName($requestData['name']);
        }

        $this->entityManager->flush();

        return $this->json($product);
    }

}
