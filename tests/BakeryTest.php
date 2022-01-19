<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Tests;


use DemosEurope\DocumentBakery\Tests\resources\Entity\Cookbook;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

class BakeryTest extends KernelTestCase
{
    /** @var EntityManagerInterface */
    private $em;

    public function setUp(): void
    {
        parent::setUp();

        touch($this->getContainer()->getParameter('kernel.project_dir').'/tests/resources/document-bakery-test-database.sqlite');

        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $this->em = $em;

        $schemaTool = $this->getContainer()->get(SchemaTool::class);
        $metadatas = $em->getMetadataFactory()->getAllMetadata();

        $schemaTool->createSchema($metadatas);

        $cookbook = new Cookbook();
        $cookbook->setFlavour('salty');
        $cookbook->setId(1);
        $cookbook->setName('Crunchy caramel treats and other afternoon snacks');

        $em->persist($cookbook);
        $em->flush();
    }

    protected function tearDown(): void
    {
        \unlink($this->getContainer()->getParameter('kernel.project_dir').'/tests/resources/document-bakery-test-database.sqlite');

        parent::tearDown();
    }

    public function testBake(): void
    {
        $repo = $this->em->getRepository(Cookbook::class);
        $entity = $repo->find(1);

        self::assertEquals('salty', $entity->getFlavour());
    }
}
