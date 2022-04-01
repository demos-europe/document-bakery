<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Recipes;


use DemosEurope\DocumentBakery\Exceptions\RecipeException;
use Traversable;
use function array_key_exists;
use function iterator_to_array;

class RecipeRepository
{
    /**
     * @var iterable|array<string,RecipeLoaderInterface>
     */
    private $loaders;

    /**
     * @var array<string, string>
     */
    private $recipeNameCache;

    /**
     * @param iterable|Traversable|RecipeLoaderInterface[] $loaders
     */
    public function __construct(iterable $loaders)
    {
        $this->loaders = iterator_to_array($loaders);
        $this->recipeNameCache = [];
    }

    public function has(string $recipeName): bool
    {
        $this->buildRecipeNameCacheIfRequired();

        return array_key_exists($recipeName, $this->recipeNameCache);
    }

    /**
     * @throws RecipeException
     */
    public function get(string $recipeName): array
    {
        if (!$this->has($recipeName)) {
            throw RecipeException::recipeNotFound($recipeName);
        }

        return $this->loaders[$this->recipeNameCache[$recipeName]]->load($recipeName);
    }

    private function buildRecipeNameCacheIfRequired(): void
    {
        if (0 === count($this->recipeNameCache)) {
            $this->buildRecipeNameCache();
        }
    }

    private function buildRecipeNameCache(): void
    {
        foreach ($this->loaders as $loaderName => $loader) {
            $availableRecipes = $loader->availableRecipes();

            foreach ($availableRecipes as $recipeName) {
                if (array_key_exists($recipeName, $this->recipeNameCache)) {
                    RecipeException::duplicateRecipeFound($recipeName);
                }

                $this->recipeNameCache[$recipeName] = $loaderName;
            }
        }
    }
}
