<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Elements;

class Title extends AbstractElement
{
    public function render(): void
    {
        $this->currentParentElement->addText($this->renderContent);
    }
}
