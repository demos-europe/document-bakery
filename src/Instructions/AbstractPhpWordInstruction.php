<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

use DemosEurope\DocumentBakery\Data\RecipeDataBag;
use DemosEurope\DocumentBakery\Exceptions\StyleException;
use PhpOffice\PhpWord\Element\AbstractElement;

abstract class AbstractPhpWordInstruction extends AbstractInstruction implements PhpWordInstructionInterface
{
    /**
     * @var AbstractElement
     */
    protected $currentParentElement;

    /**
     * @throws StyleException
     */
    public function initializeInstruction(array $instruction, RecipeDataBag $recipeDataBag): void
    {
        $this->setCurrentConfigInstruction($instruction);
        $this->setDataFromRecipeDataBag($recipeDataBag);
        $this->setStyleContent();
    }

    protected function setDataFromRecipeDataBag(RecipeDataBag $recipeDataBag): void
    {
        $this->recipeDataBag = $recipeDataBag;
        $this->currentParentElement = $recipeDataBag->getCurrentParentElement();
        $this->renderContent = $this->getRenderContent($recipeDataBag);
    }

    /**
     * @throws StyleException
     */
    protected function setStyleContent(): void
    {
        $styleContent = null;
        if (isset($this->currentConfigInstruction['style']) && 0 < count($this->currentConfigInstruction['style'])) {
            if (isset($this->currentConfigInstruction['style']['attributes'])) {
                $styleContent = $this->currentConfigInstruction['style']['attributes'];
            } elseif (isset($this->currentConfigInstruction['style']['name'])) {
                $style = $this->recipeDataBag->getStyle($this->currentConfigInstruction['style']['name']);
                $styleContent = $style['attributes'];
            } else {
                throw StyleException::noStyleInformationFoundForInstruction($this->currentConfigInstruction['name']);
            }
        }

        $this->styleContent = $styleContent;
    }
}
