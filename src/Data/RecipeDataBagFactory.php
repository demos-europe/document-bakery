<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Data;

use DemosEurope\DocumentBakery\Exceptions\RecipeException;
use DemosEurope\DocumentBakery\Recipes\RecipeRepository;

class RecipeDataBagFactory
{
    /**
     * @var RecipeRepository
     */
    private $recipeRepository;

    public function __construct(RecipeRepository $recipeRepository)
    {
        $this->recipeRepository = $recipeRepository;
    }

    /**
     * @param array<string, mixed> $queryVariables
     * @throws RecipeException
     */
    public function build(string $recipeName, array $queryVariables): RecipeWordDataBag
    {
        $recipeConfig = $this->recipeRepository->get($recipeName);

        $recipeDataBag = new RecipeWordDataBag();
        if (isset($recipeConfig['format'])) {
            $recipeDataBag->setFormat($recipeConfig['format']);
        }
        if (isset($recipeConfig['styles']) && 0 < count($recipeConfig['styles'])) {
            $recipeDataBag->setStyles($recipeConfig['styles']);
        }
        $recipeDataBag->setInstructions($recipeConfig['instructions']);
        $recipeDataBag->setQueries($recipeConfig['queries']);
        $recipeDataBag->setQueryVariables($queryVariables);

        return $recipeDataBag;
    }
}
