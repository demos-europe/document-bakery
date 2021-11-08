<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

use DemosEurope\DocumentBakery\Bakery;
use DemosEurope\DocumentBakery\TwigRenderer;

class Text extends AbstractInstruction
{
    public function render(): void
    {
        $this->currentParentElement->addText($this->renderContent);
    }
}
