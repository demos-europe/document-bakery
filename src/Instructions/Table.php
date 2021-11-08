<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

class Table extends AbstractInstruction implements StructuralInstructionInterface
{
    public function render(): void
    {
        $tableStyle = array(
            'borderColor' => '006699',
            'borderSize'  => 6,
            'cellMargin'  => 50
        );
        $table = $this->currentParentElement->addTable($tableStyle);
        $this->recipeDataBag->addToWorkingPath($table);
    }
}
