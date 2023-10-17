<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions\Word;

use DemosEurope\DocumentBakery\Instructions\StructuralInstructionInterface;

class TableRow extends AbstractPhpWordInstruction implements StructuralInstructionInterface
{
    public function render(): void
    {
        $row = $this->currentParentElement->addRow(null, $this->styleContent['row']);
        $this->recipeDataBag->addToWorkingPath($row);
    }
}
