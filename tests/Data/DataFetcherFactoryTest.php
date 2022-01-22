<?php

namespace DemosEurope\DocumentBakery\Tests\Data;

use DemosEurope\DocumentBakery\Data\DataFetcher;
use DemosEurope\DocumentBakery\Data\DataFetcherFactory;
use DemosEurope\DocumentBakery\Tests\KernelTestCase;
use DemosEurope\DocumentBakery\Tests\resources\Entity\Cookbook;
use DemosEurope\DocumentBakery\Tests\resources\ResourceType\CookbookResourceType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use EightDashThree\Querying\ConditionParsers\Drupal\DrupalFilterParser;
use EightDashThree\Wrapping\TypeProviders\PrefilledTypeProvider;

class DataFetcherFactoryTest extends KernelTestCase
{
    /**
     * @var DataFetcherFactory
     */
    private $sut;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $drupalFilterParser = $this->getContainer()->get(DrupalFilterParser::class);
        $prefilledTypeProvider = $this->getContainer()->get(PrefilledTypeProvider::class);

        // Create an entity in the DB
        $schemaTool = $this->getContainer()->get(SchemaTool::class);
        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool->createSchema($metadatas);

        $cookbook = new Cookbook();
        $cookbook->setFlavour('salty');
        $cookbook->setId(1);
        $cookbook->setName('Crunchy caramel treats and other afternoon snacks');

        $entityManager->persist($cookbook);

        $cookbook2 = new Cookbook();
        $cookbook2->setFlavour('sweet');
        $cookbook2->setId(1);
        $cookbook2->setName('Chocolate Chips Cookies');

        $entityManager->persist($cookbook2);
        $entityManager->flush();

        $this->sut = new DataFetcherFactory(
            $entityManager,
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
        self::assertInstanceOf(DataFetcher::class, $result);
    }
}
