<?php

namespace DemosEurope\DocumentBakery\Tests\Data;

use DemosEurope\DocumentBakery\Data\DataFetcher;
use DemosEurope\DocumentBakery\Tests\KernelTestCase;
use DemosEurope\DocumentBakery\Tests\resources\ResourceType\CookbookResourceType;
use EightDashThree\DqlQuerying\ObjectProviders\DoctrineOrmEntityProvider;
use EightDashThree\DqlQuerying\PropertyAccessors\ProxyPropertyAccessor;
use EightDashThree\Querying\ConditionParsers\Drupal\DrupalFilterParser;
use EightDashThree\Querying\ObjectProviders\TypeRestrictedEntityProvider;
use EightDashThree\Querying\Utilities\ConditionEvaluator;
use EightDashThree\Wrapping\Contracts\Types\ReadableTypeInterface;
use EightDashThree\Wrapping\Contracts\WrapperFactoryInterface;
use EightDashThree\Wrapping\TypeProviders\PrefilledTypeProvider;
use EightDashThree\Wrapping\Utilities\PropertyReader;
use EightDashThree\Wrapping\Utilities\SchemaPathProcessor;
use EightDashThree\Wrapping\Utilities\TypeAccessor;
use EightDashThree\Wrapping\WrapperFactories\WrapperObjectFactory;
use PHPUnit\Framework\TestCase;

class DataFetcherTest extends KernelTestCase
{
    protected $sut;
    private $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $prefilledTypeProvider = $this->getContainer()->get(PrefilledTypeProvider::class);
        $drupalFilterParser = $this->getContainer()->get(DrupalFilterParser::class);
        $resourceType = $this->getContainer()->get(CookbookResourceType::class);
        $wrapperFactory = $this->buildWrapperFactory($prefilledTypeProvider);
        $objectProvider = $this->getResourceProvider($resourceType, $prefilledTypeProvider);

        $parsedQuery = [
            'resource_type' => $resourceType,
            'filter' => []
        ];

        $this->sut = new DataFetcher($parsedQuery, $objectProvider, $wrapperFactory, $drupalFilterParser);
    }

    public function testSetNextCurrentEntity()
    {
        self::isTrue();
    }

    public function testGetDataFromPath()
    {
        self::isTrue();
    }

    public function testIsEmpty()
    {
        self::isTrue();
    }

    private function getResourceProvider(ReadableTypeInterface $ResourceType, PrefilledTypeProvider $prefilledTypeProvider): TypeRestrictedEntityProvider
    {
        $doctrineProvider = new DoctrineOrmEntityProvider(
            $ResourceType->getEntityClass(),
            $this->entityManager
        );

        $schemaProcessor = new SchemaPathProcessor($prefilledTypeProvider);

        return new TypeRestrictedEntityProvider(
            $doctrineProvider,
            $ResourceType,
            $schemaProcessor
        );
    }

    private function buildWrapperFactory(PrefilledTypeProvider $prefilledTypeProvider): WrapperObjectFactory
    {
        $propertyAccessor = new ProxyPropertyAccessor($this->entityManager);
        $schemaProcessor = new SchemaPathProcessor($prefilledTypeProvider);
        return new WrapperObjectFactory(
            new TypeAccessor($prefilledTypeProvider),
            new PropertyReader($propertyAccessor, $schemaProcessor),
            $propertyAccessor,
            new ConditionEvaluator($propertyAccessor)
        );
    }
}
