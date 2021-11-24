<?php

namespace DemosEurope\DocumentBakery\Tests\Mapper;

use DemosEurope\DocumentBakery\Mapper\PhpWordStyleOptions;
use PHPUnit\Framework\TestCase;

class PhpWordStyleOptionsTest extends TestCase
{
    public function testGetMappedStyleOptions(): void
    {
        $sut = new PhpWordStyleOptions();

        $testArray = [
            'borderBottomColor' => '00ff77',
            'borderTopSize' => 4,
            'italic' => true,
            'tblHeader' => false,
            'dash' => 'dash',
        ];

        $result = $sut->getMappedStyleOptions($testArray);

        self::assertEquals(['borderBottomColor', 'borderTopSize'], array_keys($result['section']));
        self::assertEquals(['borderBottomColor', 'borderTopSize'], array_keys($result['table']));
        self::assertEquals(['borderBottomColor', 'borderTopSize'], array_keys($result['cell']));
        self::assertEquals(['italic'], array_keys($result['font']));
        self::assertEquals(['tblHeader'], array_keys($result['row']));
        self::assertEquals(['dash'], array_keys($result['line']));
        self::assertNull($result['paragraph']);
        self::assertNull($result['image']);
        self::assertNull($result['numberingLevel']);
        self::assertNull($result['chart']);
        self::assertNull($result['toc']);
    }

}
