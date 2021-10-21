<?php

declare(strict_types=1);

namespace DemosEurope\DocumentCompiler\Elements;

use DemosEurope\DocumentCompiler\ExportDataBag;
use DemosEurope\DocumentCompiler\StructuralElementInterface;
use DemosEurope\DocumentCompiler\TwigRenderer;
use PhpOffice\PhpWord\Element\AbstractElement as PhpWordAbstractElement;

abstract class AbstractElement implements ElementInterface
{
    /**
     * @var array
     */
    protected $currentConfigElement;

    /**
     * @var PhpWordAbstractElement
     */
    protected $currentParentElement;

    /**
     * @var mixed
     */
    protected $renderContent;

    protected $exportDataBag;

    /**
     * @var TwigRenderer
     */
    protected $twigRenderer;

    public function __construct(TwigRenderer $twigRenderer)
    {
        $this->twigRenderer = $twigRenderer;
    }

    public function getCurrentConfigElement(): array
    {
        return $this->currentConfigElement;
    }

    public function setCurrentConfigElement(array $currentConfigElement): void
    {
        $this->currentConfigElement = $currentConfigElement;
    }

    public function setDataFromExportDataBag(ExportDataBag $exportDataBag): void
    {
        $this->exportDataBag = $exportDataBag;
        $this->currentParentElement = $exportDataBag->getCurrentParentElement();
        $this->renderContent = $this->getRenderContent($exportDataBag);
    }

    public function getName(): string
    {
        $className = get_class($this);
        $explodedName = explode('\\', $className);

        // return only element name, not full class name incl. namespace
        return array_pop($explodedName);
    }

    /**
     * @return mixed
     */
    protected function getRenderContent(ExportDataBag $exportDataBag)
    {
        // Only get renderContent for non-structural elements as structural elements do not render anything
        if ($this instanceof StructuralElementInterface) {
            return null;
        }

        if (isset($this->currentConfigElement['path'])) {
            $renderContent = $exportDataBag->getCurrentElementData();
        } else {
            $renderContent = $this->currentConfigElement['content'];
        }

        return $renderContent;
    }
}
