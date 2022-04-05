<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Recipes;

use DemosEurope\DocumentBakery\Data\DataFetcherFactory;
use DemosEurope\DocumentBakery\Data\RecipeWordDataBag;
use DemosEurope\DocumentBakery\Instructions\InstructionFactory;
use DemosEurope\DocumentBakery\Styles\StylesRepository;

class RecipeProcessorFactory
{
    /**
     * @var InstructionFactory
     */
    private $instructionFactory;
    /**
     * @var StylesRepository
     */
    private $stylesRepository;
    /**
     * @var DataFetcherFactory
     */
    private $dataFetcherFactory;

    public function __construct(
        DataFetcherFactory $dataFetcherFactory,
        InstructionFactory $instructionFactory,
        StylesRepository $stylesRepository)
    {
        $this->instructionFactory = $instructionFactory;
        $this->stylesRepository = $stylesRepository;
        $this->dataFetcherFactory = $dataFetcherFactory;
    }

    public function build(RecipeWordDataBag $recipeDataBag): RecipeProcessor
    {
        return new RecipeProcessor($this->dataFetcherFactory, $this->instructionFactory, $recipeDataBag, $this->stylesRepository);
    }
}
