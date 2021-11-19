<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

use DemosEurope\DocumentBakery\Data\RecipeDataBag;
use DemosEurope\DocumentBakery\Exceptions\StyleException;
use DemosEurope\DocumentBakery\TwigRenderer;
use PhpOffice\PhpWord\Element\AbstractElement;

abstract class AbstractInstruction implements InstructionInterface
{
    /**
     * @var array
     */
    protected $currentConfigInstruction;

    /**
     * @var AbstractElement
     */
    protected $currentParentElement;

    /**
     * @var mixed
     */
    protected $renderContent;

    /** @var array */
    protected $styleContent;

    /** @var RecipeDataBag */
    protected $recipeDataBag;

    /**
     * @var TwigRenderer
     */
    protected $twigRenderer;

    public function __construct(TwigRenderer $twigRenderer)
    {
        $this->twigRenderer = $twigRenderer;
    }

    /**
     * @throws StyleException
     */
    public function initializeInstruction(array $instruction, RecipeDataBag $recipeDataBag): void
    {
        $this->setCurrentConfigInstruction($instruction);
        $this->setDataFromRecipeDataBag($recipeDataBag);
        $this->setStyleContent();
    }

    protected function setCurrentConfigInstruction(array $currentConfigInstruction): void
    {
        $this->currentConfigInstruction = $currentConfigInstruction;
    }

    protected function setDataFromRecipeDataBag(RecipeDataBag $recipeDataBag): void
    {
        $this->recipeDataBag = $recipeDataBag;
        $this->currentParentElement = $recipeDataBag->getCurrentParentElement();
        $this->renderContent = $this->getRenderContent($recipeDataBag);
    }

    public static function getName(): string
    {
        $explodedName = explode('\\', static::class);

        // return only instruction name, not full class name incl. namespace
        return array_pop($explodedName);
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

    /**
     * @return mixed
     */
    protected function getRenderContent(RecipeDataBag $recipeDataBag)
    {
        // Only get renderContent for non-structural instructions as structural instructions do not render anything
        if ($this instanceof StructuralInstructionInterface) {
            return null;
        }

        if (isset($this->currentConfigInstruction['path'])) {
            $renderContent = $recipeDataBag->getCurrentInstructionData();
        } else {
            $renderContent = $this->currentConfigInstruction['content'];
        }

        return $renderContent;
    }
}
