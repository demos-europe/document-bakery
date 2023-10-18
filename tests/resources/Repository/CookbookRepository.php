<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Tests\resources\Repository;

use DemosEurope\DocumentBakery\Tests\resources\Entity\Cookbook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EDT\DqlQuerying\ConditionFactories\DqlConditionFactory;
use EDT\DqlQuerying\ObjectProviders\DoctrineOrmEntityProvider;
use EDT\JsonApi\InputHandling\RepositoryInterface;
use EDT\Querying\Pagination\PagePagination;
use InvalidArgumentException;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

class CookbookRepository extends ServiceEntityRepository implements RepositoryInterface
{
    private DoctrineOrmEntityProvider $entityProvider;
    private DqlConditionFactory $conditionFactory;

    public function __construct(
        DoctrineOrmEntityProvider $entityProvider,
        DqlConditionFactory $conditionFactory,
        ManagerRegistry $managerRegistry
    )
    {
        parent::__construct($managerRegistry, Cookbook::class);
        $this->entityProvider = $entityProvider;
        $this->conditionFactory = $conditionFactory;
    }

    public function getEntityByIdentifier(string $id, array $conditions, array $identifierPropertyPath): object
    {
        $identifierCondition = $this->conditionFactory->propertyHasValue($id, $identifierPropertyPath);
        $conditions[] = $identifierCondition;
        $entities = $this->entityProvider->getEntities($conditions, [], null);

        return match (count($entities)) {
            0       => throw new InvalidArgumentException("No matching `{$this->getEntityName()}` entity found."),
            1       => array_pop($entities),
            default => throw new InvalidArgumentException("Multiple matching `{$this->getEntityName()}` entities found.")
        };
    }

    public function getEntitiesByIdentifiers(array $identifiers, array $conditions, array $sortMethods, array $identifierPropertyPath): array
    {
        $identifierCondition = $this->conditionFactory->propertyHasAnyOfValues($identifiers, $identifierPropertyPath);
        $conditions[] = $identifierCondition;

        return $this->entityProvider->getEntities($conditions, $sortMethods, null);
    }

    public function getEntities(array $conditions, array $sortMethods): array
    {
        return $this->entityProvider->getEntities($conditions, $sortMethods, null);
    }

    public function getEntitiesForPage(array $conditions, array $sortMethods, PagePagination $pagination): Pagerfanta
    {
        $queryBuilder = $this->entityProvider->generateQueryBuilder($conditions, $sortMethods);

        $queryAdapter = new QueryAdapter($queryBuilder);
        $paginator = new Pagerfanta($queryAdapter);
        $paginator->setMaxPerPage($pagination->getSize());
        $paginator->setCurrentPage($pagination->getNumber());

        return $paginator;
    }

    public function deleteEntityByIdentifier(string $entityIdentifier, array $conditions, array $identifierPropertyPath): void
    {
    }

    public function reindexEntities(array $entities, array $conditions, array $sortMethods): array
    {
        return $entities;
    }

    public function isMatchingEntity(object $entity, array $conditions): bool
    {
        return true;
    }

    public function assertMatchingEntity(object $entity, array $conditions): void
    {
    }
}
