<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

use DemosEurope\DocumentBakery\Data\RecipeDataBag;

interface InstructionInterface
{
    public function render(): void;

    public static function getName(): string;

    public function initializeInstruction(array $instruction, RecipeDataBag $recipeDataBag): void;
}
