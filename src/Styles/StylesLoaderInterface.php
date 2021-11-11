<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Styles;

interface StylesLoaderInterface
{
    public static function getName(): string;

    public function load(string $recipeName): array;

    public function availableStyles(): array;
}
