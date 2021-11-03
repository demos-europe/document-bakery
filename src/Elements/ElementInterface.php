<?php

declare(strict_types=1);

namespace DemosEurope\DocumentCompiler\Elements;

interface ElementInterface
{
    public function render(): void;

    public static function getName(): string;
}
