<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Tests;

use DemosEurope\DocumentBakery\Bakery;
use DemosEurope\DocumentBakery\DocumentBakeryBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Nyholm\BundleTest\AppKernel;
use Nyholm\BundleTest\BaseBundleTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class DocumentBakeryBundleTest extends BaseBundleTestCase
{
    protected function createKernel(): KernelInterface
    {
        /**
         * @var AppKernel $kernel
         */
        $kernel = parent::createKernel();
        $kernel->addBundle(DoctrineBundle::class);
        $kernel->addConfigFile(__DIR__.'/resources/doctrine.yml');
        $kernel->addConfigFile(__DIR__.'/resources/services.yml');

        return $kernel;
    }
    
    public function testBundle(): void
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $container = $kernel->getContainer();

        $bakery = $container->get(Bakery::class );
        self::assertInstanceOf(Bakery::class, $bakery);
    }

    protected function getBundleClass(): string
    {
        return DocumentBakeryBundle::class;
    }
}
