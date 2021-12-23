<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

use DemosEurope\DocumentBakery\Data\RecipeDataBag;

interface InstructionInterface
{
    public function render(): void;

    /**
     * This method is used in the DocumentBakeryExtension to tag the instruction classes correctly for autowiring
     * @return string
     */
    public static function getName(): string;

    public function initializeInstruction(array $instruction, RecipeDataBag $recipeDataBag, array $mappedStyles): void;
}
