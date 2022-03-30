<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Tests;


use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use DemosEurope\DocumentBakery\DocumentBakeryBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use EightDashThree\Bundle\EightDashThreeBundle;
use Nyholm\BundleTest\AppKernel;
use Nyholm\BundleTest\BaseBundleTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Pretend to be a Kernel test case by booting the kernel via Nyholms bundle test case first.
 */
class KernelTestCase extends BaseBundleTestCase
{
    protected function createKernel(): KernelInterface
    {
        /**
         * @var AppKernel $kernel
         */
        $kernel = parent::createKernel();

        $kernel->addBundle(DoctrineBundle::class);
        $kernel->addBundle(DAMADoctrineTestBundle::class);
        $kernel->addBundle(EightDashThreeBundle::class);

        $kernel->addConfigFile(__DIR__.'/resources/doctrine.yml');
        $kernel->addConfigFile(__DIR__.'/resources/services.yml');

        return $kernel;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->createKernel();
        $this->bootKernel();
    }

    protected function getBundleClass(): string
    {
        return DocumentBakeryBundle::class;
    }
}
