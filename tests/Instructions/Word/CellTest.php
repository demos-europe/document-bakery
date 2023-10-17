<?php

namespace DemosEurope\DocumentBakery\Tests\Instructions\Word;

use DemosEurope\DocumentBakery\Instructions\Word\Cell;
use DemosEurope\DocumentBakery\Tests\Instructions\InstructionsTestCase;
use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Element\Row;
use PhpOffice\PhpWord\Element\Table;

class CellTest extends InstructionsTestCase
{
    protected function setUp(): void
    {
        $this->setInstructionUnderTest(Cell::class);
        $this->prepareData();
    }

    protected function prepareData(): void
    {
        // Instruction data is only used if the instruction has a path attribute and not content.
        $this->recipeDataBag = $this->getDefaultRecipeDataBag('Testing Text');
        $this->recipeDataBag->addToWorkingPath(new Table());
        $this->recipeDataBag->addToWorkingPath(new Row());
        $mappedStyles = $this->getDefaultMappedStyles();

        $this->prepareInstruction([], $this->recipeDataBag, $mappedStyles);
    }

    public function testIsWorkingInstance(): void
    {
        self::assertInstanceOf(Cell::class, $this->instructionUnderTest);
        $this->instructionUnderTest->render();
        /**
         * @var AbstractContainer $currentParentElement
         */
        $currentParentElement = $this->recipeDataBag->getCurrentParentElement();
        self::assertInstanceOf(\PhpOffice\PhpWord\Element\Cell::class, $currentParentElement);
    }
}
