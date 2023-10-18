<?php

namespace DemosEurope\DocumentBakery\Tests\Data;

use DemosEurope\DocumentBakery\Data\DataFetcher;
use DemosEurope\DocumentBakery\Data\DataFetcherFactory;
use DemosEurope\DocumentBakery\Tests\BakeryFunctionalTestCase;
use EDT\DqlQuerying\ConditionFactories\DqlConditionFactory;
use EDT\DqlQuerying\SortMethodFactories\SortMethodFactory;
use EDT\Querying\ConditionParsers\Drupal\DrupalFilterParser;

class DataFetcherFactoryTest extends BakeryFunctionalTestCase
{
    /**
     * @var DataFetcherFactory
     */
    private $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $conditionFactory = $this->getContainer()->get(DqlConditionFactory::class);
        $drupalFilterParser = $this->getContainer()->get(DrupalFilterParser::class);
        $sortMethodFactory = $this->getContainer()->get(SortMethodFactory::class);

        $this->sut = new DataFetcherFactory($conditionFactory, $drupalFilterParser, $sortMethodFactory);
    }

    public function testBuildWithError(): void
    {
        $parsedQuery = [
            'resource_type' => 'failingTest'
        ];

        $this->expectError();
        $this->sut->build($parsedQuery, $this->conditionFactory->true());
    }

    public function testBuildSuccessfully(): void
    {


        $parsedQuery = [
            'resource_type' => $this->resourceType,
            'filter' => []
        ];

        $result = $this->sut->build($parsedQuery);
        $flavour = $result->getDataFromPath(['flavour']);
        self::assertInstanceOf(DataFetcher::class, $result);
        self::assertNotTrue($result->isEmpty());
        self::assertEquals($this->cookbooks[0]['flavour'], $flavour);
    }
}
