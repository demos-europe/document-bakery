<?php

namespace DemosEurope\DocumentBakery\Tests\Data;

use DemosEurope\DocumentBakery\Data\DataFetcher;
use DemosEurope\DocumentBakery\Data\DataFetcherFactory;
use DemosEurope\DocumentBakery\Tests\BakeryFunctionalTestCase;
use DemosEurope\DocumentBakery\Tests\resources\ResourceType\CookbookResourceType;
use EightDashThree\Querying\ConditionParsers\Drupal\DrupalFilterParser;
use EightDashThree\Wrapping\TypeProviders\PrefilledTypeProvider;

class DataFetcherFactoryTest extends BakeryFunctionalTestCase
{
    /**
     * @var DataFetcherFactory
     */
    private $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $drupalFilterParser = $this->getContainer()->get(DrupalFilterParser::class);
        $prefilledTypeProvider = $this->getContainer()->get(PrefilledTypeProvider::class);

        $this->sut = new DataFetcherFactory(
            $this->entityManager,
            $drupalFilterParser,
            $prefilledTypeProvider
        );
    }

    public function testBuildWithError(): void
    {
        $parsedQuery = [
            'resource_type' => 'failingTest'
        ];

        $this->expectError();
        $this->sut->build($parsedQuery);
    }

    public function testBuildSuccessfully(): void
    {
        $parsedQuery = [
            'resource_type' => $this->getContainer()->get(CookbookResourceType::class),
            'filter' => []
        ];

        $result = $this->sut->build($parsedQuery);
        $flavour = $result->getDataFromPath(['flavour']);
        self::assertInstanceOf(DataFetcher::class, $result);
        self::assertNotTrue($result->isEmpty());
        self::assertEquals($this->cookbooks[0]['flavour'], $flavour);
    }
}
