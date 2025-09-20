<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use ProductService;
use ProductValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{

    /**
     * @param EntityManagerInterface $entityManager
     * @param ProductService $productService
     * @param ProductValidator $productValidator
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductService         $productService,
        private ProductValidator       $productValidator
    ) {}

    #[Route('/products', name: 'app_get_products', methods: ['GET'])]
    public function getProducts(): JsonResponse
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();

        return $this->json($products);
    }

    #[Route('/products', name: 'app_post_products', methods: ['POST'])]
    public function createProduct(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $this->productValidator->validate($requestData);

        $product = $this->productService->createProduct($requestData);

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
