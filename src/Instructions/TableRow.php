<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

class TableRow extends AbstractInstruction implements StructuralInstructionInterface
{
    public function render(): void
    {
        $row = $this->currentParentElement->addRow();
        $this->exportDataBag->addToWorkingPath($row);
    }
}
