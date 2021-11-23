<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

class TableHeader extends AbstractPhpWordInstruction
{
    public function render(): void
    {
        $row = $this->currentParentElement->addRow(null, $this->styleContent['row']);
        foreach ($this->renderContent as $header) {
            $row->addCell(null, $this->styleContent['cell'])->addText(
                $header,
                $this->styleContent['font'],
                $this->styleContent['paragraph']
            );
        }
    }
}
