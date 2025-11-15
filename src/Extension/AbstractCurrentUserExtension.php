<?php

declare(strict_types=1);

namespace App\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

abstract class AbstractCurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{

    public const FIRST_ELEMENT_ARRAY = 0;
    public const ADMIN_ROLES         = [User::ROLE_ADMIN];

    /**
     * @var Security
     */
    protected Security $security;

    /**
     * AbstractCurrentUserExtension constructor.
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass
     * @param Operation|null $operation
     * @param array $context
     * @return void
     */
    public function applyToCollection(
        QueryBuilder                $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string                      $resourceClass,
        ?Operation                  $operation = null,
        array                       $context = []
    ): void {

        if ($this->isFiltering($operation->getName(), $resourceClass)) {
            return;
        }

        $this->buildQuery($queryBuilder);
    }

    /**
     * @param $operationName
     * @param $resourceClass
     * @return bool
     */
    protected function isFiltering($operationName, $resourceClass): bool
    {
        return !$this->apply($operationName) ||
            $resourceClass !== $this->getResourceClass() ||
            ($this->security->getUser() && count(array_intersect(self::ADMIN_ROLES, $this->security->getUser()->getRoles())));
    }

    /**
     * @param $operationName
     * @return bool
     */
    protected function apply($operationName): bool
    {
        return !is_bool(strpos($operationName, "get"));
    }

    /**
     * @return string
     */
    abstract public function getResourceClass(): string;

    /**
     * @param QueryBuilder $queryBuilder
     */
    abstract public function buildQuery(QueryBuilder $queryBuilder);

    /**
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass
     * @param array $identifiers
     * @param Operation|null $operation
     * @param array $context
     */
    public function applyToItem(
        QueryBuilder                $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string                      $resourceClass,
        array                       $identifiers,
        ?Operation                  $operation = null,
        array                       $context = []
    ): void {
        if ($this->isFiltering($operation->getName(), $resourceClass)) {
            return;
        }

        $this->buildQuery($queryBuilder);
    }

}