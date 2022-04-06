<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Tests\Instructions\Word;

use DemosEurope\DocumentBakery\Instructions\Word\Text;
use DemosEurope\DocumentBakery\Tests\Instructions\InstructionsTestCase;
use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Element\Section;

class TextTest extends InstructionsTestCase
{
    protected function setUp(): void
    {
        $this->setInstructionUnderTest(Text::class);
        $this->prepareData();
    }

    protected function prepareData(): void
    {
        $instruction = [
            'name' => 'Text',
            'content' => 'Simple Test',
        ];

        // Instruction data is only used if the instruction has a path attribute and not content.
        $this->recipeDataBag = $this->getDefaultRecipeDataBag('Testing Text');
        $mappedStyles = $this->getDefaultMappedStyles();

        $this->prepareInstruction($instruction, $this->recipeDataBag, $mappedStyles);
    }

    public function testIsWorkingInstance(): void
    {
        self::assertInstanceOf(Text::class, $this->instructionUnderTest);
        $this->instructionUnderTest->render();
        /**
         * @var AbstractContainer $currentParentElement
         */
        $currentParentElement = $this->recipeDataBag->getCurrentParentElement();
        $elementList = $currentParentElement->getElements();
        /**
         * @var \PhpOffice\PhpWord\Element\Text $newElement
         */
        $newElement = $elementList[0];
        self::assertInstanceOf(Section::class, $currentParentElement);
        self::assertInstanceOf(\PhpOffice\PhpWord\Element\Text::class, $newElement);
        self::assertEquals('Simple Test', $newElement->getText());
    }
}
