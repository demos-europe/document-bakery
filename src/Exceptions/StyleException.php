<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Exceptions;

class StyleException extends \Exception
{
    public static function styleNotFound($name): self
    {
        return new self("Couldn't find style with name: '$name'");
    }

    public static function duplicateStyleFound($name): self
    {
        return new self("Found duplicate style with name: '$name'");
    }

    public static function noStyleInformationFoundForInstruction(string $instructionName): self
    {
        return new self("The style attribute was declared without either a style name or attributes for instruction '$instructionName'");
    }
}
