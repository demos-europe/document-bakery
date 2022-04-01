<?php

namespace DemosEurope\DocumentBakery\Tests\Styles;

use DemosEurope\DocumentBakery\Styles\ConfigStylesLoader;
use PHPUnit\Framework\TestCase;

class ConfigStylesLoaderTest extends TestCase
{
    /**
     * @var ConfigStylesLoader
     */
    private $sut;

    private array $testData = [
        'recipe1' => [],
        'recipe2' => [
            'format' => [],
            'instructions' => [],
            'queries' => []
        ]
    ];

    protected function setUp(): void
    {
        $this->sut = new ConfigStylesLoader($this->testData);
    }

    public function testLoad(): void
    {
        self::assertEquals($this->testData['recipe2'], $this->sut->load('recipe2'));
    }

    public function testGetName(): void
    {
        self::assertEquals(ConfigStylesLoader::class, $this->sut::getName());
    }

    public function testAvailableRecipes(): void
    {
        self::assertEquals($this->testData, $this->sut->availableStyles());
    }
}
