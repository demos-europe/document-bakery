<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Tests;

use DemosEurope\DocumentBakery\Tests\resources\Entity\Cookbook;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

class BakeryFunctionalTestCase extends KernelTestCase
{
    /**
     * @var SchemaTool|object|null
     */
    protected $schemaTool;

    /**
     * @var EntityManagerInterface|null
     */
    protected $entityManager;

    protected array $cookbooks = [
        [
            'id' => 1,
            'flavour' => 'salty',
            'name' => 'Crunchy caramel treats and other afternoon snacks',
        ],
        [
            'id' => 2,
            'flavour' => 'sweet',
            'name' => 'Chocolate Chips Cookies',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();

        // Create an entity in the DB
        $this->schemaTool = $this->getContainer()->get(SchemaTool::class);
        $this->schemaTool->createSchema($metadatas);

        $this->createCookbookEntities();
    }

    private function createCookbookEntities(): void
    {
        foreach ($this->cookbooks as $cookbookData) {
            $cookbookEntity = new Cookbook();
            $cookbookEntity->setId($cookbookData['id']);
            $cookbookEntity->setFlavour($cookbookData['flavour']);
            $cookbookEntity->setName($cookbookData['name']);

            $this->entityManager->persist($cookbookEntity);
        }
        $this->entityManager->flush();
    }

    protected function tearDown(): void
    {
        $this->schemaTool->dropDatabase();
        parent::tearDown();
    }
}
