<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

class TableHeader extends AbstractPhpWordInstruction
{
    public function render(): void
    {
        $row = $this->currentParentElement->addRow();
        foreach ($this->renderContent as $header) {
            $row->addCell()->addText($header);
        }
    }
}
