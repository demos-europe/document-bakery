<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Exceptions;

class RecipeException extends \Exception
{
    public static function recipeNotFound($name): self
    {
        return new self("Couldn't find recipe with name: '$name'");
    }

    public static function duplicateRecipeFound($name): self
    {
        return new self("Found duplicate recipe with name: '$name'");
    }
}
