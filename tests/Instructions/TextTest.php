<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Tests\Instructions;


use DemosEurope\DocumentBakery\Instructions\Text;

class TextTest extends InstructionsTestCase
{
    protected function setUp(): void
    {
        $this->setInstructionUnderTest(Text::class);
    }

    public function testIsWorkingInstance(): void
    {
        self::assertInstanceOf(Text::class, $this->instructionUnderTest);


    }
}
