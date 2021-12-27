<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery;

use DemosEurope\DocumentBakery\Data\DatapoolManagerFactory;
use DemosEurope\DocumentBakery\Data\RecipeDataBagFactory;
use DemosEurope\DocumentBakery\Exceptions\DocumentGenerationException;
use EightDashThree\Wrapping\Contracts\AccessException;
use PhpOffice\PhpWord\Writer\WriterInterface;

class Bakery
{
    private RecipeProcessorFactory $recipeProcessorFactory;
    private DatapoolManagerFactory $datapoolManagerFactory;
    private RecipeDataBagFactory $recipeDataBagFactory;

    public function __construct(
        DatapoolManagerFactory $datapoolManagerFactory,
        RecipeDataBagFactory $recipeDataBagFactory,
        RecipeProcessorFactory $recipeProcessorFactory
    )
    {
        $this->recipeProcessorFactory = $recipeProcessorFactory;
        $this->datapoolManagerFactory = $datapoolManagerFactory;
        $this->recipeDataBagFactory = $recipeDataBagFactory;
    }

    /**
     * @param array<string, string> $queryVariables
     * @throws DocumentGenerationException
     * @throws AccessException|Exceptions\StyleException
     */
    public function create(string $recipeName, array $queryVariables): ?WriterInterface
    {
        $recipeDataBag = $this->recipeDataBagFactory->build($recipeName);
        $datapoolManager = $this->datapoolManagerFactory->build($recipeDataBag->getQueries(), $queryVariables);
        $recipeProcessor = $this->recipeProcessorFactory->build($datapoolManager, $recipeDataBag);

        return $recipeProcessor->createFromRecipe();
    }
}
