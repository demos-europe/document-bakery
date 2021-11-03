<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Elements;

interface ElementInterface
{
    public function render(): void;

    public static function getName(): string;
}
