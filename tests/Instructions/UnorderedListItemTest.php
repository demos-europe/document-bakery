<?php

namespace DemosEurope\DocumentBakery\Tests\Instructions;

use DemosEurope\DocumentBakery\Instructions\UnorderedListItem;
use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Element\ListItem;
use PhpOffice\PhpWord\Element\Section;

class UnorderedListItemTest extends InstructionsTestCase
{
    protected function setUp(): void
    {
        $this->setInstructionUnderTest(UnorderedListItem::class);
        $this->prepareData();
    }

    protected function prepareData(): void
    {
        $instruction = [
            'name' => 'UnorderedListItem',
            'content' => 'Simple Test',
        ];

        // Instruction data is only used if the instruction has a path attribute and not content.
        $this->recipeDataBag = $this->getDefaultRecipeDataBag('Testing Text');
        $mappedStyles = $this->getDefaultMappedStyles();

        $this->prepareInstruction($instruction, $this->recipeDataBag, $mappedStyles);
    }

    public function testIsWorkingInstance(): void
    {
        self::assertInstanceOf(UnorderedListItem::class, $this->instructionUnderTest);
        $this->instructionUnderTest->render();
        /**
         * @var AbstractContainer $currentParentElement
         */
        $currentParentElement = $this->recipeDataBag->getCurrentParentElement();
        $elementList = $currentParentElement->getElements();
        /**
         * @var ListItem $newElement
         */
        $newElement = $elementList[0];
        self::assertInstanceOf(Section::class, $currentParentElement);
        self::assertInstanceOf(ListItem::class, $newElement);
        self::assertEquals('Simple Test', $newElement->getText());
    }
}
