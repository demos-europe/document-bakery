<?php

declare(strict_types=1);

namespace DemosInternational\DocumentCompiler\Elements;

use DemosInternational\DocumentCompiler\StructuralElementInterface;

class TableRow extends AbstractElement implements StructuralElementInterface
{
    public function render(): void
    {
        $row = $this->currentParentElement->addRow();
        $this->exportDataBag->addToWorkingPath($row);
    }
}
