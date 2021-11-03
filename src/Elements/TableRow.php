<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Elements;

use DemosEurope\DocumentBakery\StructuralElementInterface;

class TableRow extends AbstractElement implements StructuralElementInterface
{
    public function render(): void
    {
        $row = $this->currentParentElement->addRow();
        $this->exportDataBag->addToWorkingPath($row);
    }
}
