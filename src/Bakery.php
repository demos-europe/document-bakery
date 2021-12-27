<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery;

use DemosEurope\DocumentBakery\Data\DatapoolManagerFactory;
use DemosEurope\DocumentBakery\Data\RecipeDataBag;
use DemosEurope\DocumentBakery\Exceptions\DocumentGenerationException;
use DemosEurope\DocumentBakery\Recipes\RecipeRepository;
use EightDashThree\Wrapping\Contracts\AccessException;
use PhpOffice\PhpWord\Writer\WriterInterface;

class Bakery
{
    private RecipeRepository $recipeRepository;

    private RecipeProcessorFactory $recipeProcessorFactory;
    private DatapoolManagerFactory $datapoolManagerFactory;

    public function __construct(
        DatapoolManagerFactory $datapoolManagerFactory,
        RecipeProcessorFactory $recipeProcessorFactory,
        RecipeRepository       $recipeRepository
    )
    {
        $this->recipeRepository = $recipeRepository;
        $this->recipeProcessorFactory = $recipeProcessorFactory;
        $this->datapoolManagerFactory = $datapoolManagerFactory;
    }

    /**
     * @param array<string, string> $queryVariables
     * @throws DocumentGenerationException
     * @throws AccessException|Exceptions\StyleException
     */
    public function create(string $recipeName, array $queryVariables): ?WriterInterface
    {
        $recipeConfig = $this->recipeRepository->get($recipeName);

        $recipeDataBag = $this->getRecipeDataBag($recipeConfig);
        $datapoolManager = $this->datapoolManagerFactory->build($recipeConfig['queries'], $queryVariables);
        $recipeProcessor = $this->recipeProcessorFactory->build($datapoolManager, $recipeDataBag);

        return $recipeProcessor->createFromRecipe();
    }

    /**
     * @param array<string, mixed> $recipeConfig
     */
    private function getRecipeDataBag(array $recipeConfig): RecipeDataBag
    {
        $recipeDataBag = new RecipeDataBag();
        if (isset($recipeConfig['format'])) {
            $recipeDataBag->setFormat($recipeConfig['format']);
        }
        if (isset($recipeConfig['styles']) && 0 < count($recipeConfig['styles'])) {
            $recipeDataBag->setStyles($recipeConfig['styles']);
        }
        $recipeDataBag->setInstructions($recipeConfig['instructions']);

        return $recipeDataBag;
    }
}
