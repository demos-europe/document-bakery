<?php

namespace DemosEurope\DocumentBakery\Tests\Recipes;

use DemosEurope\DocumentBakery\Exceptions\RecipeException;
use DemosEurope\DocumentBakery\Recipes\ConfigRecipeLoader;
use DemosEurope\DocumentBakery\Recipes\RecipeRepository;
use DemosEurope\DocumentBakery\Tests\BakeryFunctionalTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class RecipeRepositoryTest extends BakeryFunctionalTestCase
{
    /**
     * @var RecipeRepository
     */
    protected $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $recipeConfigLoader = new ConfigRecipeLoader($this->config['recipes']);
        $configIterable = new ArrayCollection([$recipeConfigLoader]);
        $this->sut = new RecipeRepository($configIterable);
    }

    public function testHas(): void
    {
        // Unsuccessful
        $result1 = $this->sut->has('wrongRecipe');
        self::assertNotTrue($result1);
        // Successful
        $result2 = $this->sut->has('RecipeWithoutStyles');
        self::assertTrue($result2);
    }

    public function testGetException(): void
    {
        $this->expectException(RecipeException::class);
        $this->sut->get('wrongRecipe');
    }

    public function testGetSuccessful(): void
    {
        $result = $this->sut->get('RecipeWithoutStyles');
        self::assertEquals($this->config['recipes']['RecipeWithoutStyles'], $result);
    }
}
