<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Tests;

use DemosEurope\DocumentBakery\Bakery;

class DocumentBakeryBundleTest extends KernelTestCase
{
    public function testBundle(): void
    {
        $this->setUp();

        $container = $this->getContainer();

        $bakery = $container->get(Bakery::class);
        self::assertInstanceOf(Bakery::class, $bakery);
    }
}
