<?php

declare(strict_types=1);

namespace DemosEurope\DocumentCompiler\Elements;

use DemosEurope\DocumentCompiler\StructuralElementInterface;

class Table extends AbstractElement implements StructuralElementInterface
{
    public function render(): void
    {
        $tableStyle = array(
            'borderColor' => '006699',
            'borderSize'  => 6,
            'cellMargin'  => 50
        );
        $table = $this->currentParentElement->addTable($tableStyle);
        $this->exportDataBag->addToWorkingPath($table);
    }
}
