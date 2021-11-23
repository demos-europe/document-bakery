<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

use PhpOffice\PhpWord\Style\ListItem;

class UnorderedListItem extends AbstractPhpWordInstruction
{

    public function render(): void
    {
        $this->currentParentElement->addListItem(
            $this->renderContent,
            0,
            $this->styleContent['font'],
            ListItem::TYPE_BULLET_FILLED,
            $this->styleContent['paragraph']
        );
    }
}
