<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

class Cell extends AbstractInstruction implements StructuralInstructionInterface
{
    public function render(): void
    {
        $cell = $this->currentParentElement->addCell();
        $this->recipeDataBag->addToWorkingPath($cell);
    }
}