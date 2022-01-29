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

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();

        // Create an entity in the DB
        $this->schemaTool = $this->getContainer()->get(SchemaTool::class);
        $this->schemaTool->createSchema($metadatas);

        $cookbook = new Cookbook();
        $cookbook->setFlavour('salty');
        $cookbook->setId(1);
        $cookbook->setName('Crunchy caramel treats and other afternoon snacks');

        $this->entityManager->persist($cookbook);

        $cookbook2 = new Cookbook();
        $cookbook2->setFlavour('sweet');
        $cookbook2->setId(2);
        $cookbook2->setName('Chocolate Chips Cookies');

        $this->entityManager->persist($cookbook2);
        $this->entityManager->flush();
    }

    protected function tearDown(): void
    {
        $this->schemaTool->dropDatabase();
        parent::tearDown();
    }
}
