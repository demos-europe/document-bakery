<?php

namespace DemosEurope\DocumentBakery\Tests\Instructions\Word;

use DemosEurope\DocumentBakery\Instructions\Word\TableRow;
use DemosEurope\DocumentBakery\Tests\Instructions\InstructionsTestCase;
use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Element\Row;
use PhpOffice\PhpWord\Element\Table;

class TableRowTest extends InstructionsTestCase
{
    protected function setUp(): void
    {
        $this->setInstructionUnderTest(TableRow::class);
        $this->prepareData();
    }

    protected function prepareData(): void
    {
        // Instruction data is only used if the instruction has a path attribute and not content.
        $this->recipeDataBag = $this->getDefaultRecipeDataBag('Testing Text');
        $this->recipeDataBag->addToWorkingPath(new Table());
        $mappedStyles = $this->getDefaultMappedStyles();

        $this->prepareInstruction([], $this->recipeDataBag, $mappedStyles);
    }

    public function testIsWorkingInstance(): void
    {
        self::assertInstanceOf(TableRow::class, $this->instructionUnderTest);
        $this->instructionUnderTest->render();
        /**
         * @var AbstractContainer $currentParentElement
         */
        $currentParentElement = $this->recipeDataBag->getCurrentParentElement();
        self::assertInstanceOf(Row::class, $currentParentElement);
    }
}
