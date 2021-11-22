<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

class TableRow extends AbstractPhpWordInstruction implements StructuralInstructionInterface
{
    public function render(): void
    {
        $row = $this->currentParentElement->addRow();
        $this->recipeDataBag->addToWorkingPath($row);
    }
}
