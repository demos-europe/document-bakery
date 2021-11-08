<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

class StaticText extends AbstractInstruction
{
    public function render(): void
    {
        $renderContent = $this->twigRenderer->render($this->renderContent);
        $this->currentParentElement->addText($renderContent);
    }
}
