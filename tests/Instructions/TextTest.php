<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Tests\Instructions;


use DemosEurope\DocumentBakery\Data\RecipeDataBag;
use DemosEurope\DocumentBakery\Instructions\Text;
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

        $this->recipeDataBag = new RecipeDataBag();
        $this->recipeDataBag->setCurrentInstructionData('TestingText');
        $this->recipeDataBag->setInstructions(
            [
                [
                    'name' => 'Text',
                    'content' => 'Simple Test',
                ]
            ]
        );
        $this->recipeDataBag->setFormat(
            [
                'name' => 'A4LandscapeFormat',
                'options' => [],
                'output' => 'docx'
            ]
        );

        $mappedStyles = [
            'section' => NULL,
            'font' => NULL,
            'paragraph' => NULL,
            'table' => NULL,
            'row' => NULL,
            'cell' => NULL,
            'image' => NULL,
            'numberingLevel' => NULL,
            'chart' => NULL,
            'toc' => NULL,
            'line' => NULL,
        ];

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
