<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Exceptions;

class ExportConfigException extends \Exception
{
    public static function exportDefinitionNotFound($name): self
    {
        return new self("Couldn't find export definition with name: '$name'");
    }
}
