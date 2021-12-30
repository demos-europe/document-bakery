<?php

namespace DemosEurope\DocumentBakery\Tests\Data;

use DemosEurope\DocumentBakery\Data\DataFetcherFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DataFetcherFactoryTest extends KernelTestCase
{
    private $sut;
    protected function setUp(): void
    {
        static::bootKernel();
        $this->sut = static::getContainer()->get(DataFetcherFactory::class);
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
