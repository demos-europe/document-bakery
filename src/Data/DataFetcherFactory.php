<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Data;

use Doctrine\ORM\EntityManagerInterface;
use EightDashThree\DqlQuerying\ObjectProviders\DoctrineOrmEntityProvider;
use EightDashThree\DqlQuerying\PropertyAccessors\ProxyPropertyAccessor;
use EightDashThree\Querying\ConditionParsers\Drupal\DrupalFilterParser;
use EightDashThree\Querying\ObjectProviders\TypeRestrictedEntityProvider;
use EightDashThree\Querying\Utilities\ConditionEvaluator;
use EightDashThree\Wrapping\Contracts\Types\ReadableTypeInterface;
use EightDashThree\Wrapping\TypeProviders\PrefilledTypeProvider;
use EightDashThree\Wrapping\Utilities\PropertyReader;
use EightDashThree\Wrapping\Utilities\SchemaPathProcessor;
use EightDashThree\Wrapping\Utilities\TypeAccessor;
use EightDashThree\Wrapping\WrapperFactories\WrapperObjectFactory;

class DataFetcherFactory
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var PrefilledTypeProvider
     */
    private $prefilledTypeProvider;
    /**
     * @var DrupalFilterParser
     */
    private $drupalFilterParser;
    /**
     * @var WrapperObjectFactory
     */
    private $wrapperFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        DrupalFilterParser $drupalFilterParser,
        PrefilledTypeProvider $prefilledTypeProvider)
    {
        $this->entityManager = $entityManager;
        $this->prefilledTypeProvider = $prefilledTypeProvider;
        $this->drupalFilterParser = $drupalFilterParser;
        $this->buildWrapperFactory();
    }

    public function build(array $parsedQuery): DataFetcher
    {
        $objectProvider = $this->getResourceProvider($parsedQuery['resource_type']);
        return new DataFetcher($parsedQuery, $objectProvider, $this->wrapperFactory, $this->drupalFilterParser);
    }

    private function getResourceProvider(ReadableTypeInterface $ResourceType): TypeRestrictedEntityProvider
    {
        $doctrineProvider = new DoctrineOrmEntityProvider(
            $ResourceType->getEntityClass(),
            $this->entityManager
        );

        $schemaProcessor = new SchemaPathProcessor($this->prefilledTypeProvider);

        return new TypeRestrictedEntityProvider(
            $doctrineProvider,
            $ResourceType,
            $schemaProcessor
        );
    }

    private function buildWrapperFactory(): void
    {
        $propertyAccessor = new ProxyPropertyAccessor($this->entityManager);
        $schemaProcessor = new SchemaPathProcessor($this->prefilledTypeProvider);
        $this->wrapperFactory = new WrapperObjectFactory(
            new TypeAccessor($this->prefilledTypeProvider),
            new PropertyReader($propertyAccessor, $schemaProcessor),
            $propertyAccessor,
            new ConditionEvaluator($propertyAccessor)
        );
    }
}
