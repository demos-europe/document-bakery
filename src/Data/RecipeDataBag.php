<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Data;

use DemosEurope\DocumentBakery\Exceptions\StyleException;
use DemosEurope\DocumentBakery\Styles\StylesRepository;
use PhpOffice\PhpWord\Element\AbstractElement;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;

class RecipeDataBag
{
    /**
     * @var array
     */
    private $format;

    /**
     * @var array
     */
    private $workingPath;

    /**
     * @var mixed
     */
    private $currentInstructionData;

    /** @var StylesRepository */
    private $stylesRepository;

    public function __construct()
    {
        $this->format = [];
        $this->workingPath = [];

        $this->initializePhpWord();
    }

    public function getPhpWordObject(): PhpWord
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

    public function setStylesRepository(StylesRepository $stylesRepository): void
    {
        $this->stylesRepository = $stylesRepository;
    }

    /**
     * @throws StyleException
     */
    public function getStyle(string $styleName): array
    {
        return $this->stylesRepository->get($styleName);
    }

    public function getCurrentParentElement(): AbstractElement
    {
        return end($this->workingPath);
    }

    private function initializePhpWord(): void
    {
        $phpWord = new PhpWord();
        Settings::setOutputEscapingEnabled(true);
        $phpWord->getSettings()->setUpdateFields(true);

        $this->workingPath[] = $phpWord;
        $this->workingPath[] = $phpWord->addSection();
    }

    public function addToWorkingPath(AbstractElement $instruction): void
    {
        $this->workingPath[] = $instruction;
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
}
