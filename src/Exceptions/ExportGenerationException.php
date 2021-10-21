<?php

declare(strict_types=1);

namespace DemosEurope\DocumentCompiler\Exceptions;

class ExportGenerationException extends \Exception
{
    public static function elementNotFound($elementName): self
    {
        return new self("Couldn't find Element: '$elementName'");
    }

    public static function noValueForPlaceholder($placeholder): self
    {
        return new self("Could not replace placeholder \''$placeholder'\' as there is no value for it given in queryVariables.");
    }

    public static function writerObjectGenerationFailed(): self
    {
        return new self("The writer object returned as null, something went wrong.");
    }
}
