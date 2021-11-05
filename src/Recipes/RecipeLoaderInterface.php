<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Recipes;


interface RecipeLoaderInterface
{
    public static function getName(): string;

    public function load(string $recipeName): array;

    public function availableRecipes(): array;
}
