<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Elements;

class StaticText extends AbstractElement
{
    public function render(): void
    {
        $renderContent = $this->twigRenderer->render($this->renderContent);
        $this->currentParentElement->addText($renderContent);
    }
}
