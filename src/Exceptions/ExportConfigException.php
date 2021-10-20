<?php

declare(strict_types=1);

namespace DemosInternational\DocumentCompiler\Exceptions;

class ExportConfigException extends \Exception
{
    public static function exportDefinitionNotFound($name): self
    {
        return new self("Couldn't find export definition with name: '$name'");
    }
}
