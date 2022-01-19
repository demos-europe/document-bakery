<?php

namespace DemosEurope\DocumentBakery\Tests\Data;

use DemosEurope\DocumentBakery\Data\DataFetcherFactory;
use DemosEurope\DocumentBakery\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use EightDashThree\Querying\ConditionParsers\Drupal\DrupalFilterParser;
use EightDashThree\Wrapping\TypeProviders\PrefilledTypeProvider;

class DataFetcherFactoryTest extends KernelTestCase
{
    private $sut;
    protected function setUp(): void
    {
        parent::setUp();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $drupalFilterParser = $this->getContainer()->get(DrupalFilterParser::class);
        $prefilledTypeProvider = $this->getContainer()->get(PrefilledTypeProvider::class);

        $this->sut = new DataFetcherFactory(
            $entityManager,
            $drupalFilterParser,
            $prefilledTypeProvider
        );
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
