<?php

namespace DemosEurope\DocumentBakery\Tests\Instructions;

use DemosEurope\DocumentBakery\Exceptions\DocumentGenerationException;
use DemosEurope\DocumentBakery\Instructions\InstructionFactory;
use DemosEurope\DocumentBakery\Instructions\Word\Text;
use DemosEurope\DocumentBakery\Instructions\Word\UnorderedListItem;
use DemosEurope\DocumentBakery\TwigRenderer;
use PHPUnit\Framework\TestCase;

class InstructionFactoryTest extends TestCase
{

    public function testLookupForName()
    {
        $twigRenderer = new TwigRenderer();
        $instructions = [
            'Text' => new Text($twigRenderer),
            'UnorderedListItem' => new UnorderedListItem($twigRenderer)
        ];

        $sut = new InstructionFactory(new \ArrayObject($instructions));

        $testObject1 = $sut->lookupForName('Text');
        self::assertEquals($instructions['Text'], $testObject1);

        $this->expectException(DocumentGenerationException::class);
        $testObject2 = $sut->lookupForName('WrongName');
    }
}
