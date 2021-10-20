<?php

declare(strict_types=1);

namespace DemosInternational\DocumentCompiler\Elements;

interface ElementInterface
{
    public function render(): void;

    public function getName(): string;
}
