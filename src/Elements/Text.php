<?php

declare(strict_types=1);

namespace DemosEurope\DocumentCompiler\Elements;

use DemosEurope\DocumentCompiler\Exporter;
use DemosEurope\DocumentCompiler\TwigRenderer;

class Text extends AbstractElement
{
    public function render(): void
    {
        $this->currentParentElement->addText($this->renderContent);
    }
}
