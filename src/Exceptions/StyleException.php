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
}
