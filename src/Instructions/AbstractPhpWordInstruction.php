<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

use DemosEurope\DocumentBakery\Data\RecipeDataBag;
use DemosEurope\DocumentBakery\Exceptions\StyleException;
use DemosEurope\DocumentBakery\Mapper\PhpWordStyleOptions;
use PhpOffice\PhpWord\Element\AbstractElement;

abstract class AbstractPhpWordInstruction extends AbstractInstruction implements PhpWordInstructionInterface
{
    protected AbstractElement $currentParentElement;

    /**
     * @param array<string, mixed> $instruction
     * @throws StyleException
     */
    public function initializeInstruction(array $instruction, RecipeDataBag $recipeDataBag, array $mappedStyles): void
    {
        $this->setCurrentConfigInstruction($instruction);
        $this->setDataFromRecipeDataBag($recipeDataBag);
        $this->setStyleContent($mappedStyles);
    }

    protected function setDataFromRecipeDataBag(RecipeDataBag $recipeDataBag): void
    {
        $this->recipeDataBag = $recipeDataBag;
        $this->currentParentElement = $recipeDataBag->getCurrentParentElement();
        $this->renderContent = $this->getRenderContent($recipeDataBag);
    }
}
