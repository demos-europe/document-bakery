<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery;

use DemosEurope\DocumentBakery\Data\DatapoolManager;
use DemosEurope\DocumentBakery\Data\RecipeDataBag;
use DemosEurope\DocumentBakery\Instructions\InstructionFactory;
use DemosEurope\DocumentBakery\Styles\StylesRepository;

class RecipeProcessorFactory
{
    private InstructionFactory $instructionFactory;
    private StylesRepository $stylesRepository;

    public function __construct(InstructionFactory $instructionFactory, StylesRepository $stylesRepository)
    {
        $this->instructionFactory = $instructionFactory;
        $this->stylesRepository = $stylesRepository;
    }

    public function build(DatapoolManager $datapoolManager, RecipeDataBag $recipeDataBag): RecipeProcessor
    {
        return new RecipeProcessor($datapoolManager, $this->instructionFactory, $recipeDataBag, $this->stylesRepository);
    }
}
