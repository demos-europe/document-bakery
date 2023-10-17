<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions\Spreadsheet;

class Text extends AbstractSpreadsheetInstruction
{
    public function render(): void
    {
        $currentCounters = $this->recipeDataBag->getCurrentCounters();
        $this->currentParentElement->getActiveSheet()->setCellValueByColumnAndRow(
            $currentCounters["row"],
            $currentCounters["column"],
            $this->renderContent
        );
    }
}
