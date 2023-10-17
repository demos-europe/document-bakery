<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions\Spreadsheet;

class Cell extends AbstractSpreadsheetInstruction
{
    public function render(): void
    {
        $currentCounters = $this->recipeDataBag->getCurrentCounters();
        $this->recipeDataBag->setCurrentCounters(
            $currentCounters["row"],
            $currentCounters["column"]+1
        );
    }
}
