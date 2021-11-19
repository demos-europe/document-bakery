<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

class Table extends AbstractInstruction implements StructuralInstructionInterface
{
    public function render(): void
    {
        $table = $this->currentParentElement->addTable($this->styleContent);
        $this->recipeDataBag->addToWorkingPath($table);
    }
}
