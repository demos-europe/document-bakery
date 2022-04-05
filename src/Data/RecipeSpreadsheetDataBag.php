<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Data;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use stdClass;

class RecipeSpreadsheetDataBag implements RecipeDataBagInterface
{

    private $format;

    private $instructions;

    private $queries;

    private $queryVariables;

    private $styles;

    private $workingPath;

    /**
     * @var mixed
     */
    private $currentInstructionData;

    public function __construct()
    {
        $this->format = [];
        $this->instructions = [];
        $this->queries = [];
        $this->queryVariables = [];
        $this->styles = [];
        $this->workingPath = [];

        $this->initializePhpSpreadsheet();
    }

    public function getWriterObject(): Spreadsheet
    {
        return $this->workingPath[0];
    }

    public function getFormat(): array
    {
        return $this->format;
    }

    public function setFormat(array $format): void
    {
        $this->format = $format;
    }

    public function getCurrentParentElement(): stdClass
    {
        return end($this->workingPath);
    }

    private function initializePhpSpreadsheet(): void
    {
        $phpSpreadsheet = new Spreadsheet();

        $this->workingPath[] = $phpSpreadsheet;
    }

    /**
     * @param stdClass $phpOfficeElement
     */
    public function addToWorkingPath($phpOfficeElement): void
    {
        $this->workingPath[] = $phpOfficeElement;
    }

    public function removeFromWorkingPath(): void
    {
        array_pop($this->workingPath);
    }

    /**
     * @return mixed
     */
    public function getCurrentInstructionData()
    {
        return $this->currentInstructionData;
    }

    /**
     * @param mixed $currentInstructionData
     */
    public function setCurrentInstructionData($currentInstructionData): void
    {
        $this->currentInstructionData = $currentInstructionData;
    }

    /**
     * @return array
     */
    public function getInstructions(): array
    {
        return $this->instructions;
    }

    /**
     * @param array $instructions
     */
    public function setInstructions(array $instructions): void
    {
        $this->instructions = $instructions;
    }

    /**
     * @return array
     */
    public function getStyles(): array
    {
        return $this->styles;
    }

    /**
     * @param array $styles
     */
    public function setStyles(array $styles): void
    {
        $this->styles = $styles;
    }

    /**
     * @return array
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

    /**
     * @return array
     */
    public function getQueryByName(string $queryName): array
    {
        return $this->queries[$queryName];
    }

    /**
     * @param array $queries
     */
    public function setQueries(array $queries): void
    {
        $this->queries = $queries;
    }

    /**
     * @return array
     */
    public function getQueryVariables(): array
    {
        return $this->queryVariables;
    }

    /**
     * @param array $queryVariables
     */
    public function setQueryVariables(array $queryVariables): void
    {
        $this->queryVariables = $queryVariables;
    }
}
