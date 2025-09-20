<?php

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductService
{
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    /**
     * @param array $data
     * @return Product
     */
    public function createProduct(array $data): Product
    {
        $product = new Product();

        $product
            ->setName($data['name'])
            ->setDescription($data['description'])
            ->setPrice($data['price']);

        $this->entityManager->persist($product);

        return $product;
    }

}