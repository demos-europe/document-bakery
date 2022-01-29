<?php

namespace DemosEurope\DocumentBakery\Tests\Data;

use DemosEurope\DocumentBakery\Data\DataFetcher;
use DemosEurope\DocumentBakery\Tests\BakeryFunctionalTestCase;
use DemosEurope\DocumentBakery\Tests\resources\ResourceType\CookbookResourceType;
use EightDashThree\DqlQuerying\ObjectProviders\DoctrineOrmEntityProvider;
use EightDashThree\DqlQuerying\PropertyAccessors\ProxyPropertyAccessor;
use EightDashThree\Querying\ConditionParsers\Drupal\DrupalFilterParser;
use EightDashThree\Querying\ObjectProviders\TypeRestrictedEntityProvider;
use EightDashThree\Querying\Utilities\ConditionEvaluator;
use EightDashThree\Wrapping\Contracts\AccessException;
use EightDashThree\Wrapping\Contracts\Types\ReadableTypeInterface;
use EightDashThree\Wrapping\TypeProviders\PrefilledTypeProvider;
use EightDashThree\Wrapping\Utilities\PropertyReader;
use EightDashThree\Wrapping\Utilities\SchemaPathProcessor;
use EightDashThree\Wrapping\Utilities\TypeAccessor;
use EightDashThree\Wrapping\WrapperFactories\WrapperObjectFactory;

class DataFetcherTest extends BakeryFunctionalTestCase
{
    protected $sut;

    protected function setUp(): void
    {
        parent::setUp();

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

    public function testSetNextCurrentEntity(): void
    {
        $idBefore = $this->sut->getDataFromPath(['id']);
        $this->sut->setNextCurrentEntity();
        $idAfter = $this->sut->getDataFromPath(['id']);

        self::assertEquals($this->cookbooks[0]['id'], $idBefore);
        self::assertEquals($this->cookbooks[1]['id'], $idAfter);
    }

    public function testGetDataFromPath(): void
    {
        $id = $this->sut->getDataFromPath(['id']);
        $flavour = $this->sut->getDataFromPath(['flavour']);
        $name = $this->sut->getDataFromPath(['name']);

        self::assertEquals($this->cookbooks[0]['id'], $id);
        self::assertEquals($this->cookbooks[0]['flavour'], $flavour);
        self::assertEquals($this->cookbooks[0]['name'], $name);
    }

    public function testGetDataFromPathException(): void
    {
        $this->expectException(AccessException::class);
        $this->sut->getDataFromPath(['nonsense']);
    }

    public function testIsEmpty(): void
    {
        self::assertNotTrue($this->sut->isEmpty());
        $this->sut->setNextCurrentEntity();
        self::assertTrue($this->sut->isEmpty());
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
