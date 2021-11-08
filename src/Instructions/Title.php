<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

class Title extends AbstractInstruction
{
    public function render(): void
    {
        $this->currentParentElement->addText($this->renderContent);
    }
}
