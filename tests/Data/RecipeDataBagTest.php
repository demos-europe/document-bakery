<?php

namespace DemosEurope\DocumentBakery\Tests\Data;

use DemosEurope\DocumentBakery\Data\RecipeDataBag;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\PhpWord;
use PHPUnit\Framework\TestCase;

class RecipeDataBagTest extends TestCase
{
    private RecipeDataBag $sut;

    protected function resetSut(): void
    {
        $this->sut = new RecipeDataBag();
    }

    public function testAddToWorkingPath(): void
    {
        $this->resetSut();

        $newTableElement = new Table();
        $this->sut->addToWorkingPath($newTableElement);
        self::assertEquals($newTableElement, $this->sut->getCurrentParentElement());
    }

    public function testRemoveFromWorkingPath(): void
    {
        $this->resetSut();
        $newTableElement = new Table();
        $this->sut->addToWorkingPath($newTableElement);

        $this->sut->removeFromWorkingPath();
        self::assertInstanceOf(Section::class, $this->sut->getCurrentParentElement());
    }

    public function testGetFormat(): void
    {
        $this->resetSut();

        self::assertEquals([], $this->sut->getFormat());
    }

    public function testSetFormat()
    {
        $this->resetSut();

        $expected = [
            'format' => 'landscape',
            'other' => 'stuff'
        ];
        $this->sut->setFormat($expected);
        self::assertEquals($expected, $this->sut->getFormat());
    }

    public function testGetCurrentInstructionData(): void
    {
        $this->resetSut();

        self::assertEquals('', $this->sut->getCurrentInstructionData());
    }

    public function testSetCurrentInstructionData(): void
    {
        $this->resetSut();

        $expected = 'testData';
        $this->sut->setCurrentInstructionData($expected);
        self::assertEquals($expected, $this->sut->getCurrentInstructionData());

        $expected2 = [
            'format' => 'landscape',
            'other' => 'stuff'
        ];
        $this->sut->setCurrentInstructionData($expected2);
        self::assertEquals($expected2, $this->sut->getCurrentInstructionData());
    }

    public function testGetPhpWordObject(): void
    {
        $this->resetSut();

        self::assertInstanceOf(PhpWord::class, $this->sut->getPhpWordObject());
    }

    public function testGetCurrentParentElement(): void
    {
        $this->resetSut();

        self::assertInstanceOf(Section::class, $this->sut->getCurrentParentElement());
    }

    public function testGetInstructions(): void
    {
        $this->resetSut();

        self::assertEquals([], $this->sut->getInstructions());
    }

    public function testSetInstructions(): void
    {
        $this->resetSut();

        $expected = [
            [
                'name' => 'Text',
                'content' => 'Test Text'
            ],
            [
                'name' => 'Text',
                'path' => 'path.property'
            ]
        ];
        $this->sut->setInstructions($expected);
        self::assertEquals($expected, $this->sut->getInstructions());
    }

    public function testGetStyles(): void
    {
        $this->resetSut();

        self::assertEquals([], $this->sut->getStyles());
    }

    public function testSetStyles(): void
    {
        $this->resetSut();

        $expected = [
            'format' => 'landscape',
            'other' => 'stuff'
        ];
        $this->sut->setStyles($expected);
        self::assertEquals($expected, $this->sut->getStyles());
    }
}