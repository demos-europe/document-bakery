<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

class StaticText extends AbstractPhpWordInstruction
{
    public function render(): void
    {
        $this->currentParentElement->addText($this->renderContent, $this->styleContent['font'], $this->styleContent['paragraph']);
    }
}
