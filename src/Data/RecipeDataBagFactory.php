<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Data;

use DemosEurope\DocumentBakery\Recipes\RecipeRepository;

class RecipeDataBagFactory
{
    private RecipeRepository $recipeRepository;

    public function __construct(RecipeRepository $recipeRepository)
    {
        $this->recipeRepository = $recipeRepository;
    }

    public function build(string $recipeName): RecipeDataBag
    {
        $recipeConfig = $this->recipeRepository->get($recipeName);

        $recipeDataBag = new RecipeDataBag();
        if (isset($recipeConfig['format'])) {
            $recipeDataBag->setFormat($recipeConfig['format']);
        }
        if (isset($recipeConfig['styles']) && 0 < count($recipeConfig['styles'])) {
            $recipeDataBag->setStyles($recipeConfig['styles']);
        }
        $recipeDataBag->setInstructions($recipeConfig['instructions']);
        $recipeDataBag->setQueries($recipeConfig['queries']);

        return $recipeDataBag;
    }
}
