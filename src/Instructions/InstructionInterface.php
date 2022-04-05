<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

use DemosEurope\DocumentBakery\Data\RecipeDataBagInterface;

interface InstructionInterface
{
    public function render(): void;

    /**
     * This method is used in the DocumentBakeryExtension to tag the instruction classes correctly for autowiring
     * @return string
     */
    public static function getName(): string;

    public function initializeInstruction(array $instruction, RecipeDataBagInterface $recipeDataBag, array $mappedStyles): void;
}
