<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions\Word;

class CellSimpleText extends AbstractPhpWordInstruction
{
    public function render(): void
    {
        $this->currentParentElement->addCell(null, $this->styleContent['cell'])
            ->addText(
                $this->renderContent,
                $this->styleContent['font'],
                $this->styleContent['paragraph']
            );
    }
}
