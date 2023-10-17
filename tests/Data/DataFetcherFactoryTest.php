<?php

namespace DemosEurope\DocumentBakery\Tests\Data;

use DemosEurope\DocumentBakery\Data\DataFetcher;
use DemosEurope\DocumentBakery\Data\DataFetcherFactory;
use DemosEurope\DocumentBakery\Tests\BakeryFunctionalTestCase;

class DataFetcherFactoryTest extends BakeryFunctionalTestCase
{
    /**
     * @var DataFetcherFactory
     */
    private $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new DataFetcherFactory();
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

        $result = $this->sut->build($parsedQuery, $this->conditionFactory->true());
        $flavour = $result->getDataFromPath(['flavour']);
        self::assertInstanceOf(DataFetcher::class, $result);
        self::assertNotTrue($result->isEmpty());
        self::assertEquals($this->cookbooks[0]['flavour'], $flavour);
    }
}
