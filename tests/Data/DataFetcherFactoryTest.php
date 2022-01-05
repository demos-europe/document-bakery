<?php

namespace DemosEurope\DocumentBakery\Tests\Data;

use DemosEurope\DocumentBakery\Data\DataFetcherFactory;
use DemosEurope\DocumentBakery\Tests\KernelTestCase;

class DataFetcherFactoryTest extends KernelTestCase
{
    private $sut;
    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = $this->getContainer()->get(DataFetcherFactory::class);
    }

    public function testBuild(): void
    {
        $parsedQuery = [
            'resource_type' => 'failingTest'
        ];

        $this->expectError();
        $this->sut->build($parsedQuery);
    }
}
