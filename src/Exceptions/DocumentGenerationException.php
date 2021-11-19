<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Exceptions;

class DocumentGenerationException extends \Exception
{
    public static function instructionNotFound($instructionName): self
    {
        return new self("Couldn't find instruction: '$instructionName'");
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
