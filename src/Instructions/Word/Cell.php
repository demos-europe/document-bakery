<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions\Word;

use DemosEurope\DocumentBakery\Instructions\StructuralInstructionInterface;

class Cell extends AbstractPhpWordInstruction implements StructuralInstructionInterface
{
    public function render(): void
    {
        $cell = $this->currentParentElement->addCell(null, $this->styleContent['cell']);
        $this->recipeDataBag->addToWorkingPath($cell);
    }
}
