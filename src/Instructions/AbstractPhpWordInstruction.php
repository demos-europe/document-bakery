<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

use DemosEurope\DocumentBakery\Data\RecipeDataBag;
use DemosEurope\DocumentBakery\Exceptions\StyleException;
use DemosEurope\DocumentBakery\Mapper\PhpWordStyleOptions;
use DemosEurope\DocumentBakery\TwigRenderer;
use PhpOffice\PhpWord\Element\AbstractElement;

abstract class AbstractPhpWordInstruction extends AbstractInstruction implements PhpWordInstructionInterface
{
    protected AbstractElement $currentParentElement;

    private PhpWordStyleOptions $phpWordStylesMapper;

    public function __construct(PhpWordStyleOptions $phpWordStylesMapper, TwigRenderer $twigRenderer)
    {
        parent::__construct($twigRenderer);
        $this->phpWordStylesMapper = $phpWordStylesMapper;
    }

    /**
     * @param array<string, mixed> $instruction
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
        $styleContent = [];
        if (isset($this->currentConfigInstruction['style']) && 0 < count($this->currentConfigInstruction['style'])) {
            // get attributes of style
            if (isset($this->currentConfigInstruction['style']['name'])) {
                $style = $this->recipeDataBag->getStyle($this->currentConfigInstruction['style']['name']);
                $styleContent = $style['attributes'];
            }
            // get local style attributes and merge them into existing styles
            if (isset($this->currentConfigInstruction['style']['attributes'])) {
                $styleContent = array_replace_recursive($styleContent, $this->currentConfigInstruction['style']['attributes']);
            }
        }

        // Now we need to map the attributes to the possible phpWord style sets
        $this->styleContent = $this->phpWordStylesMapper->getMappedStyleOptions($styleContent);
    }
}
