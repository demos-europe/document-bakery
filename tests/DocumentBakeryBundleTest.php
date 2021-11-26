<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Tests;

use DemosEurope\DocumentBakery\Bakery;
use DemosEurope\DocumentBakery\DocumentBakeryBundle;
use Nyholm\BundleTest\BaseBundleTestCase;

class DocumentBakeryBundleTest extends BaseBundleTestCase
{
    public function testBundle(): void
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $container = $kernel->getContainer();

        $bundle = $container->get(Bakery::class );
        self::assertInstanceOf(Bakery::class, $bundle);
    }

    protected function getBundleClass(): string
    {
        return DocumentBakeryBundle::class;
    }
}
