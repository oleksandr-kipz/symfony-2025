<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function findByExampleField(string $name): array
    {
        return $this->createQueryBuilder('product')
            ->andWhere('product.name = :name')
            ->setParameter('name', $name)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array $filterData
     * @param int $itemsPerPage
     * @param int $page
     * @return mixed
     */
    #[ArrayShape([
        'products'       => "mixed",
        'totalPageCount' => "float",
        'totalItems'     => "int"
    ])] public function getProducts(array $filterData, int $itemsPerPage, int $page): mixed
    {
        $queryBuilder = $this->createQueryBuilder('product');

        if (isset($filterData['name'])) {
            $queryBuilder->andWhere('product.name LIKE :name')
                ->setParameter('name', '%' . $filterData['name'] . '%');
        }

        if (isset($filterData['price']['lte'])) {
            $queryBuilder->andWhere('product.price <= :price')
                ->setParameter('price', $filterData['price']['lte']);
        }

        if (isset($filterData['price']['lt'])) {
            $queryBuilder->andWhere('product.price < :price')
                ->setParameter('price', $filterData['price']['lt']);
        }

        if (isset($filterData['price']['gte'])) {
            $queryBuilder->andWhere('product.price >= :price')
                ->setParameter('price',  $filterData['price']['gte']);
        }

        if (isset($filterData['price']['gt'])) {
            $queryBuilder->andWhere('product.price > :price')
                ->setParameter('price',  $filterData['price']['gt']);
        }

        $paginator = new Paginator ($queryBuilder);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $itemsPerPage);

        $paginator
            ->getQuery()
            ->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'products'       => $paginator->getQuery()->getResult(),
            'totalPageCount' => $pagesCount,
            'totalItems'     => $totalItems
        ];
    }

}
