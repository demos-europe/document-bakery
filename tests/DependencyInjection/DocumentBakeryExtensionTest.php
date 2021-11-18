<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Tests\DependencyInjection;

use DemosEurope\DocumentBakery\DependencyInjection\DocumentBakeryExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class DocumentBakeryExtensionTest extends AbstractExtensionTestCase
{
    public function testPassesConfigurationToBundleConfig(): void
    {
        $this->load();

        self::assertTrue($this->container->hasExtension('document_bakery'));
    }

    protected function getContainerExtensions(): array
    {
        return [
            new DocumentBakeryExtension(),
        ];
    }
}
