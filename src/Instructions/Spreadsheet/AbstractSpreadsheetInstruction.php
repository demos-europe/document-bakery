<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions\Spreadsheet;

use DemosEurope\DocumentBakery\Data\RecipeDataBagInterface;
use DemosEurope\DocumentBakery\Data\RecipeSpreadsheetDataBag;
use DemosEurope\DocumentBakery\Instructions\AbstractInstruction;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

abstract class AbstractSpreadsheetInstruction extends AbstractInstruction implements SpreadsheetInstructionInterface
{
    /**
     * @var Spreadsheet
     */
    protected $currentParentElement;

    /**
     * @var RecipeSpreadsheetDataBag
     */
    protected $recipeDataBag;

    protected function setDataFromRecipeDataBag(RecipeDataBagInterface $recipeDataBag): void
    {
        $this->recipeDataBag = $recipeDataBag;
        $this->currentParentElement = $recipeDataBag->getCurrentParentElement();
        $this->renderContent = $this->getRenderContent($recipeDataBag);
    }

    public function initializeInstruction(array $instruction, RecipeDataBagInterface $recipeDataBag, array $mappedStyles): void
    {
        $this->setCurrentConfigInstruction($instruction);
        $this->setDataFromRecipeDataBag($recipeDataBag);
        $this->setStyleContent($mappedStyles);
    }
}
