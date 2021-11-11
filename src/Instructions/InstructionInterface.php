<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

interface InstructionInterface
{
    public function render(): void;

    public static function getName(): string;
}
