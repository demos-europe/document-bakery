<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

class CellSimpleText extends AbstractInstruction
{
    public function render(): void
    {
        $this->currentParentElement->addCell()->addText($this->renderContent);
    }
}