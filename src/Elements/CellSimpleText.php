<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Elements;

class CellSimpleText extends AbstractElement
{
    public function render(): void
    {
        $this->currentParentElement->addCell()->addText($this->renderContent);
    }
}
