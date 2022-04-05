<?php

namespace DemosEurope\DocumentBakery\Data;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpWord\Element\AbstractElement;
use PhpOffice\PhpWord\PhpWord;

interface RecipeDataBagInterface
{
    /**
     * @return PhpWord|Spreadsheet
     */
    public function getWriterObject();

    public function getFormat(): array;

    public function setFormat(array $format): void;

    public function getCurrentParentElement(): AbstractElement;

    public function addToWorkingPath(AbstractElement $phpWordElement): void;

    public function removeFromWorkingPath(): void;

    /**
     * @return mixed
     */
    public function getCurrentInstructionData();

    /**
     * @param mixed $currentInstructionData
     */
    public function setCurrentInstructionData($currentInstructionData): void;

    /**
     * @return array<int|string, array>
     */
    public function getInstructions(): array;

    /**
     * @param array $instructions
     */
    public function setInstructions(array $instructions): void;

    /**
     * @return array
     */
    public function getStyles(): array;

    /**
     * @param array $styles
     */
    public function setStyles(array $styles): void;

    /**
     * @return array
     */
    public function getQueries(): array;

    /**
     * @return array
     */
    public function getQueryByName(string $queryName): array;

    /**
     * @param array $queries
     */
    public function setQueries(array $queries): void;

    /**
     * @return array
     */
    public function getQueryVariables(): array;

    /**
     * @param array $queryVariables
     */
    public function setQueryVariables(array $queryVariables): void;
}
