<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Elements;

use DemosEurope\DocumentBakery\StructuralElementInterface;

class Cell extends AbstractElement implements StructuralElementInterface
{
    public function render(): void
    {
        $cell = $this->currentParentElement->addCell();
        $this->exportDataBag->addToWorkingPath($cell);
    }
}
