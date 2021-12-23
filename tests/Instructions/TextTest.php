<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Tests\Instructions;


use DemosEurope\DocumentBakery\Data\RecipeDataBag;
use DemosEurope\DocumentBakery\Instructions\Text;

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

        $recipeDataBag = new RecipeDataBag();
        $recipeDataBag->setCurrentInstructionData('TestingText');
        $recipeDataBag->setInstructions(
            [
                [
                    'name' => 'Text',
                    'content' => 'Simple Test',
                ]
            ]
        );
        $recipeDataBag->setFormat(
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

        $this->prepareInstruction($instruction, $recipeDataBag, $mappedStyles);
    }

    public function testIsWorkingInstance(): void
    {
        self::assertInstanceOf(Text::class, $this->instructionUnderTest);
    }
}
