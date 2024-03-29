<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery;

use DemosEurope\DocumentBakery\Data\RecipeDataBagFactory;
use DemosEurope\DocumentBakery\Exceptions\DocumentGenerationException;
use DemosEurope\DocumentBakery\Recipes\RecipeProcessorFactory;
use EDT\Wrapping\Contracts\AccessException;
use PhpOffice\PhpWord\Writer\WriterInterface;

class Bakery
{
    /**
     * @var RecipeProcessorFactory
     */
    private $recipeProcessorFactory;
    /**
     * @var RecipeDataBagFactory
     */
    private $recipeDataBagFactory;

    public function __construct(
        RecipeDataBagFactory $recipeDataBagFactory,
        RecipeProcessorFactory $recipeProcessorFactory
    )
    {
        $this->recipeProcessorFactory = $recipeProcessorFactory;
        $this->recipeDataBagFactory = $recipeDataBagFactory;
    }

    /**
     * @param array<string, string> $queryVariables
     * @throws DocumentGenerationException
     * @throws AccessException|Exceptions\StyleException
     */
    public function create(string $recipeName, array $queryVariables): ?WriterInterface
    {
        $recipeDataBag = $this->recipeDataBagFactory->build($recipeName, $queryVariables);
        return $this->recipeProcessorFactory->build($recipeDataBag)->createFromRecipe();
    }
}
