<?php

declare(strict_types=1);

namespace DemosEurope\DocumentCompiler\Elements;

class Text extends AbstractElement
{
    public function render(): void
    {
        $this->currentParentElement->addText($this->renderContent);
    }
}
