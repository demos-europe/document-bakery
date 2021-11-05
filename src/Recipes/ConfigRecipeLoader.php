<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Recipes;


use function array_keys;

/**
 * Load recipes provided in the `recipes` configuration key if this is
 * used as a Symfony Bundle.
 */
class ConfigRecipeLoader implements RecipeLoaderInterface
{
    /**
     * @var array<string, array>
     */
    private $recipes;

    public function __construct(array $recipes)
    {
        $this->recipes = $recipes;
    }

    public static function getName(): string
    {
        return self::class;
    }

    public function load(string $recipeName): array
    {
        return $this->recipes[$recipeName];
    }

    public function availableRecipes(): array
    {
        return array_keys($this->recipes);
    }
}
