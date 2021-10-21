<?php

declare(strict_types=1);

namespace DemosEurope\DocumentCompiler\Elements;

class TableHeader extends AbstractElement
{
    public function render(): void
    {
        $row = $this->currentParentElement->addRow();
        foreach ($this->renderContent as $header) {
            $row->addCell()->addText($header);
        }
    }
}
