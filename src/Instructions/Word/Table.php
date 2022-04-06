<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions\Word;

use DemosEurope\DocumentBakery\Instructions\StructuralInstructionInterface;

class Table extends AbstractPhpWordInstruction implements StructuralInstructionInterface
{
    public function render(): void
    {
        $table = $this->currentParentElement->addTable($this->styleContent['table']);
        $this->recipeDataBag->addToWorkingPath($table);
    }
}
