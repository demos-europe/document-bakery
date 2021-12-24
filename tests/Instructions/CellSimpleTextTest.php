<?php

namespace DemosEurope\DocumentBakery\Tests\Instructions;

use DemosEurope\DocumentBakery\Instructions\CellSimpleText;
use PhpOffice\PhpWord\Element\Cell;
use PhpOffice\PhpWord\Element\Row;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Text;

class CellSimpleTextTest extends InstructionsTestCase
{
    protected function setUp(): void
    {
        $this->setInstructionUnderTest(CellSimpleText::class);
        $this->prepareData();
    }

    protected function prepareData(): void
    {
        $instruction = [
            'name' => 'CellSimpleText',
            'content' => 'Simple Test',
        ];

        // Instruction data is only used if the instruction has a path attribute and not content.
        $this->recipeDataBag = $this->getDefaultRecipeDataBag('Testing Text');
        $this->recipeDataBag->addToWorkingPath(new Table());
        $this->recipeDataBag->addToWorkingPath(new Row());
        $mappedStyles = $this->getDefaultMappedStyles();

        $this->prepareInstruction($instruction, $this->recipeDataBag, $mappedStyles);
    }

    public function testIsWorkingInstance(): void
    {
        self::assertInstanceOf(CellSimpleText::class, $this->instructionUnderTest);
        $this->instructionUnderTest->render();
        /**
         * @var Row $currentParentElement
         */
        $currentParentElement = $this->recipeDataBag->getCurrentParentElement();
        $cellsList = $currentParentElement->getCells();
        $cell = $cellsList[0];
        $textList = $cell->getElements();
        /**
         * @var Text $text
         */
        $text = $textList[0];
        self::assertInstanceOf(Row::class, $currentParentElement);
        self::assertInstanceOf(Cell::class, $cell);
        self::assertInstanceOf(Text::class, $text);
        self::assertEquals('Simple Test', $text->getText());
    }
}
