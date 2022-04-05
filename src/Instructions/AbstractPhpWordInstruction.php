<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

use DemosEurope\DocumentBakery\Data\RecipeDataBagInterface;
use PhpOffice\PhpWord\Element\AbstractElement;

abstract class AbstractPhpWordInstruction extends AbstractInstruction implements PhpWordInstructionInterface
{
    /**
     * @var AbstractElement
     */
    protected $currentParentElement;

    /**
     * @param array<string, mixed> $instruction
     */
    public function initializeInstruction(array $instruction, RecipeDataBagInterface $recipeDataBag, array $mappedStyles): void
    {
        $this->setCurrentConfigInstruction($instruction);
        $this->setDataFromRecipeDataBag($recipeDataBag);
        $this->setStyleContent($mappedStyles);
    }

    protected function setDataFromRecipeDataBag(RecipeDataBagInterface $recipeDataBag): void
    {
        $this->recipeDataBag = $recipeDataBag;
        $this->currentParentElement = $recipeDataBag->getCurrentParentElement();
        $this->renderContent = $this->getRenderContent($recipeDataBag);
    }
}
