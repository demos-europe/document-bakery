<?php

namespace DemosEurope\DocumentBakery\Tests\Recipes;

use DemosEurope\DocumentBakery\Recipes\ConfigRecipeLoader;
use PHPUnit\Framework\TestCase;

class ConfigRecipeLoaderTest extends TestCase
{
    private ConfigRecipeLoader $sut;

    protected function setUp(): void
    {
        $this->sut = new ConfigRecipeLoader([
            'recipe1' => [],
            'recipe2' => [
                'format' => [],
                'instructions' => [],
                'queries' => []
            ]
        ]);
    }

    public function testLoad(): void
    {
        $expected = [
            'format' => [],
            'instructions' => [],
            'queries' => []
        ];

        self::assertEquals($expected, $this->sut->load('recipe2'));
    }

    public function testGetName(): void
    {
        self::assertEquals(ConfigRecipeLoader::class, $this->sut::getName());
    }

    public function testAvailableRecipes(): void
    {
        self::assertEquals(['recipe1', 'recipe2'], $this->sut->availableRecipes());
    }
}
