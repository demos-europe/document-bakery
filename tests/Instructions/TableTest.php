<?php

namespace DemosEurope\DocumentBakery\Tests\Instructions;

use DemosEurope\DocumentBakery\Instructions\Table;
use PhpOffice\PhpWord\Element\AbstractContainer;

class TableTest extends InstructionsTestCase
{
    protected function setUp(): void
    {
        $this->setInstructionUnderTest(Table::class);
        $this->prepareData();
    }

    protected function prepareData(): void
    {
        // Instruction data is only used if the instruction has a path attribute and not content.
        $this->recipeDataBag = $this->getDefaultRecipeDataBag('Testing Text');
        $mappedStyles = $this->getDefaultMappedStyles();

        $this->prepareInstruction([], $this->recipeDataBag, $mappedStyles);
    }

    public function testIsWorkingInstance(): void
    {
        self::assertInstanceOf(Table::class, $this->instructionUnderTest);
        $this->instructionUnderTest->render();
        /**
         * @var AbstractContainer $currentParentElement
         */
        $currentParentElement = $this->recipeDataBag->getCurrentParentElement();
        self::assertInstanceOf(\PhpOffice\PhpWord\Element\Table::class, $currentParentElement);
    }
}
