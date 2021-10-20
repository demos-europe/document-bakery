<?php

declare(strict_types=1);

namespace DemosInternational\DocumentCompiler\Elements;

class CellSimpleText extends AbstractElement
{
    public function render(): void
    {
        $this->currentParentElement->addCell()->addText($this->renderContent);
    }
}
