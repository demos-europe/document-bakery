<?php

namespace DemosEurope\DocumentBakery\Tests\Data;

use DemosEurope\DocumentBakery\Data\DataFetcher;
use DemosEurope\DocumentBakery\Tests\BakeryFunctionalTestCase;
use EDT\Wrapping\Contracts\AccessException;
class DataFetcherTest extends BakeryFunctionalTestCase
{
    protected $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $parsedQuery = [
            'resource_type' => $this->resourceType,
            'filter' => []
        ];

        $this->sut = new DataFetcher($this->resourceType, $this->conditionFactory->true(), [], 5, true);
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
        $name = $this->sut->getDataFromPath(['name']);
        $flavour = $this->sut->getDataFromPath(['flavour']);

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
}
