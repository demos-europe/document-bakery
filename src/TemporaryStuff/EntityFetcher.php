<?php

declare(strict_types=1);

namespace DemosEurope\DocumentCompiler\TemporaryStuff;

use Doctrine\ORM\EntityManagerInterface;
use EightDashThree\DqlQuerying\ConditionFactories\DqlConditionFactory;
use EightDashThree\DqlQuerying\ObjectProviders\DoctrineOrmEntityProvider;
use EightDashThree\DqlQuerying\PropertyAccessors\ProxyPropertyAccessor;
use EightDashThree\Querying\ObjectProviders\TypeRestrictedEntityProvider;
use EightDashThree\Querying\Utilities\ConditionEvaluator;
use EightDashThree\Wrapping\Contracts\Types\ReadableTypeInterface;
use EightDashThree\Wrapping\TypeProviders\PrefilledTypeProvider;
use EightDashThree\Wrapping\Utilities\PropertyReader;
use EightDashThree\Wrapping\Utilities\SchemaPathProcessor;
use EightDashThree\Wrapping\Utilities\TypeAccessor;
use EightDashThree\Wrapping\WrapperFactories\WrapperObject;
use EightDashThree\Wrapping\WrapperFactories\WrapperObjectFactory;

class EntityFetcher
{
    /**
     * @var DqlConditionFactory
     */
    private $dqlConditionFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    private $wrapperObjectFactory;
    /**
     * @var PrefilledTypeProvider
     */
    private $prefilledTypeProvider;

    public function __construct(
        DqlConditionFactory $dqlConditionFactory,
        EntityManagerInterface $entityManager,
        PrefilledTypeProvider $prefilledTypeProvider
    )
    {
        $this->dqlConditionFactory = $dqlConditionFactory;
        $this->entityManager = $entityManager;
        $this->prefilledTypeProvider = $prefilledTypeProvider;
    }

    public function getResourceProvider($ResourceType): TypeRestrictedEntityProvider
    {
        $doctrineProvider = new DoctrineOrmEntityProvider(
            $ResourceType->getEntityClass(),
            $this->entityManager
        );

        $typeAccessor = new TypeAccessor($this->prefilledTypeProvider);

        $schemaProcessor = new SchemaPathProcessor($this->prefilledTypeProvider);

        $propertyAccessor = new ProxyPropertyAccessor($this->entityManager);
        $this->wrapperObjectFactory = new WrapperObjectFactory(
            $typeAccessor,
            new PropertyReader($propertyAccessor, $schemaProcessor),
            $propertyAccessor,
            new ConditionEvaluator($propertyAccessor)
        );

        return new TypeRestrictedEntityProvider(
            $doctrineProvider,
            $ResourceType,
            $schemaProcessor
        );
    }

    public function wrapEntity($entity, ReadableTypeInterface $resourceType): WrapperObject
    {
        return $this->wrapperObjectFactory->createWrapper($entity, $resourceType);
    }

}

