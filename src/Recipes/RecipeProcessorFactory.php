<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Recipes;

use DemosEurope\DocumentBakery\Data\DataFetcherFactory;
use DemosEurope\DocumentBakery\Data\RecipeDataBag;
use DemosEurope\DocumentBakery\Instructions\InstructionFactory;
use DemosEurope\DocumentBakery\Styles\StylesRepository;

class RecipeProcessorFactory
{
    private InstructionFactory $instructionFactory;
    private StylesRepository $stylesRepository;
    private DataFetcherFactory $dataFetcherFactory;

    public function __construct(
        DataFetcherFactory $dataFetcherFactory,
        InstructionFactory $instructionFactory,
        StylesRepository $stylesRepository)
    {
        $this->instructionFactory = $instructionFactory;
        $this->stylesRepository = $stylesRepository;
        $this->dataFetcherFactory = $dataFetcherFactory;
    }

    public function build(RecipeDataBag $recipeDataBag): RecipeProcessor
    {
        return new RecipeProcessor($this->dataFetcherFactory, $this->instructionFactory, $recipeDataBag, $this->stylesRepository);
    }
}
