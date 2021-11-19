<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

use DemosEurope\DocumentBakery\Data\RecipeDataBag;
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

    public function getCurrentConfigInstruction(): array
    {
        return $this->currentConfigInstruction;
    }

    public function setCurrentConfigInstruction(array $currentConfigInstruction): void
    {
        $this->currentConfigInstruction = $currentConfigInstruction;
    }

    public function setDataFromRecipeDataBag(RecipeDataBag $recipeDataBag): void
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
