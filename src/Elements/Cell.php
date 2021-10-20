<?php

declare(strict_types=1);

namespace DemosInternational\DocumentCompiler\Elements;

use DemosInternational\DocumentCompiler\StructuralElementInterface;

class Cell extends AbstractElement implements StructuralElementInterface
{
    public function render(): void
    {
        $cell = $this->currentParentElement->addCell();
        $this->exportDataBag->addToWorkingPath($cell);
    }
}
