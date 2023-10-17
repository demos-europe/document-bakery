<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Tests\resources\ResourceType;

use DemosEurope\DocumentBakery\Tests\resources\Entity\Cookbook;
use EDT\JsonApi\InputHandling\RepositoryInterface;
use EDT\JsonApi\OutputHandling\DynamicTransformer;
use EDT\JsonApi\RequestHandling\ExpectedPropertyCollection;
use EDT\JsonApi\RequestHandling\MessageFormatter;
use EDT\JsonApi\RequestHandling\ModifiedEntity;
use EDT\JsonApi\ResourceTypes\GetableTypeInterface;
use EDT\PathBuilding\End;
use EDT\PathBuilding\PropertyAutoPathInterface;
use EDT\PathBuilding\PropertyAutoPathTrait;
use EDT\Querying\Contracts\PropertyAccessorInterface;
use EDT\Querying\Pagination\PagePagination;
use EDT\Wrapping\Contracts\Types\FetchableTypeInterface;
use EDT\Wrapping\Contracts\Types\TransferableTypeInterface;
use EDT\Wrapping\EntityDataInterface;
use EDT\Wrapping\PropertyBehavior\Attribute\PathAttributeReadability;
use EDT\Wrapping\PropertyBehavior\Identifier\PathIdentifierReadability;
use EDT\Wrapping\ResourceBehavior\ResourceReadability;
use EDT\Wrapping\ResourceBehavior\ResourceUpdatability;
use EDT\Wrapping\Utilities\AttributeTypeResolverInterface;
use League\Fractal\TransformerAbstract;
use Pagerfanta\Pagerfanta;

/**
 * @property-read End $id
 * @property-read End $name
 * @property-read End $flavour
 */
class CookbookResourceType implements FetchableTypeInterface, GetableTypeInterface, TransferableTypeInterface, PropertyAutoPathInterface
{
    use PropertyAutoPathTrait;
    public function __construct(
        protected readonly PropertyAccessorInterface $propertyAccessor,
        protected readonly RepositoryInterface $repository,
        protected readonly AttributeTypeResolverInterface $attributeTypeResolver
    )
    {
    }

    public function getEntities(array $conditions, array $sortMethods): array
    {
        return $this->repository->getEntities($conditions, $sortMethods);
    }

    public function getEntitiesForPage(array $conditions, array $sortMethods, PagePagination $pagination): Pagerfanta
    {
        return $this->repository->getEntitiesForPage($conditions, $sortMethods, $pagination);
    }

    public function getEntity(string $identifier): object
    {
        return $this->repository->getEntityByIdentifier($identifier, [], $this->id->getAsNames());
    }

    public function getTypeName(): string
    {
        return 'Cookbook';
    }

    public function getTransformer(): TransformerAbstract
    {
        return new DynamicTransformer($this, new MessageFormatter(), null);
    }

    public function getEntityClass(): string
    {
        return Cookbook::class;
    }

    public function assertMatchingEntity(object $entity, array $conditions): void
    {
        $this->repository->assertMatchingEntity($entity, $conditions);
    }

    public function isMatchingEntity(object $entity, array $conditions): bool
    {
        return $this->repository->isMatchingEntity($entity, $conditions);
    }

    public function getReadability(): ResourceReadability
    {
        return new ResourceReadability(
            [
                'name' => new PathAttributeReadability($this->getEntityClass(), $this->name, true, $this->propertyAccessor, $this->attributeTypeResolver),
                'flavour' => new PathAttributeReadability($this->getEntityClass(), $this->flavour, true, $this->propertyAccessor, $this->attributeTypeResolver)
            ],
            [],
            [],
            new PathIdentifierReadability($this->getEntityClass(), $this->id, $this->propertyAccessor)
        );
    }

    public function getUpdatability(): ResourceUpdatability
    {
        return new ResourceUpdatability([], [], []);
    }

    public function reindexEntities(array $entities, array $conditions, array $sortMethods): array
    {
        return $this->repository->reindexEntities($entities, $conditions, $sortMethods);
    }

    public function getEntitiesForRelationship(array $identifiers, array $conditions, array $sortMethods): array
    {
        return $this->repository->getEntitiesByIdentifiers($identifiers, $conditions, $sortMethods, $this->id->getAsNames());
    }

    public function getEntityForRelationship(string $identifier, array $conditions): object
    {
        return $this->repository->getEntityByIdentifier($identifier, $conditions, $this->id->getAsNames());
    }

    public function getExpectedUpdateProperties(): ExpectedPropertyCollection
    {
        return $this->getUpdatability()->getExpectedProperties();
    }

    public function updateEntity(string $entityId, EntityDataInterface $entityData): ModifiedEntity
    {
        throw new \Exception('Not implemented');
    }
}
