<?php

declare(strict_types=1);

namespace DemosEurope\DocumentCompiler;

use PhpOffice\PhpWord\Element\AbstractElement;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;

class ExportDataBag
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
    private $currentElementData;

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

    public function addToWorkingPath(AbstractElement $element): void
    {
        $this->workingPath[] = $element;
    }

    public function removeFromWorkingPath(): void
    {
        array_pop($this->workingPath);
    }

    /**
     * @return mixed
     */
    public function getCurrentElementData()
    {
        return $this->currentElementData;
    }

    /**
     * @param mixed $currentElementData
     */
    public function setCurrentElementData($currentElementData): void
    {
        $this->currentElementData = $currentElementData;
    }
}
