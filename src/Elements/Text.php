<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Elements;

use DemosEurope\DocumentBakery\Exporter;
use DemosEurope\DocumentBakery\TwigRenderer;

class Text extends AbstractElement
{
    public function render(): void
    {
        $this->currentParentElement->addText($this->renderContent);
    }
}
