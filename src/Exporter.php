<?php

declare(strict_types=1);

namespace DemosInternational\DocumentCompiler;

use DemosInternational\DocumentCompiler\Config\ExportConfigTreeBuilder;
use DemosInternational\DocumentCompiler\Exceptions\ExportConfigException;
use DemosInternational\DocumentCompiler\Exceptions\ExportGenerationException;
use DemosInternational\DocumentCompiler\TemporaryStuff\EntityFetcher;
use Doctrine\ORM\Mapping\Entity;
use EightDashThree\Querying\ConditionParsers\Drupal\DrupalFilterParser;
use EightDashThree\Wrapping\Contracts\AccessException;
use EightDashThree\Wrapping\Utilities\GenericEntityFetcher;
use Exception;
use PhpOffice\PhpWord\Element\AbstractElement;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Writer\WriterInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Yaml;

class Exporter
{
    /**
     * @var ElementFactory
     */
    private $elementFactory;

    /**
     * @var AbstractElement
     */
    public $currentStructureElement;

    /**
     * @var ExportDataBag
     */
    private $exportDataBag;

    /**
     * @var DatapoolManager
     */
    private $datapoolManager;

    /**
     * @var EntityFetcher
     */
    private $entityFetcher;
    /**
     * @var DrupalFilterParser
     */
    private $drupalFilterParser;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(
        ElementFactory $elementFactory,
        EntityFetcher $entityFetcher,
        DrupalFilterParser $drupalFilterParser,
        ParameterBagInterface $parameterBag
    )
    {
        $this->elementFactory = $elementFactory;
        $this->exportDataBag = new ExportDataBag();
        $this->entityFetcher = $entityFetcher;
        $this->drupalFilterParser = $drupalFilterParser;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @throws ExportGenerationException
     * @throws \PhpOffice\PhpWord\Exception\Exception
     * @throws AccessException
     */
    public function create(string $exportName, array $queryVariables): ?WriterInterface
    {
        $exportConfig = $this->getExportConfig($exportName);

        $this->datapoolManager = new DatapoolManager(
            $exportConfig['queries'],
            $queryVariables,
            $this->entityFetcher,
            $this->drupalFilterParser
        );
        if (isset($exportConfig['format'])) {
            $this->exportDataBag->setFormat($exportConfig['format']);
        }
        $this->processElements($exportConfig['elements']);

        $writerObject = IOFactory::createWriter($this->exportDataBag->getPhpWordObject(), 'Word2007');
        if (null === $writerObject) {
            throw ExportGenerationException::writerObjectGenerationFailed();
        }
        return $writerObject;
    }

    /**
     * @param array $elements
     * @throws ExportGenerationException|AccessException
     */
    private function processElements(array $elements): void
    {
        foreach ($elements as $element) {
            $elementClass = $this->elementFactory->lookupForName($element['name']);

            // How to handle options? Do they have to be declared like in phpWord?
            // And then can just be handed over.
            // Or do we need a mapper to map from options to correct phpWord styles?

            // handle data lookup
            if (array_key_exists('path', $element)) {
                [$datapool, $pathArray] = $this->datapoolManager->parsePath($element['path']);
                $this->setCurrentElementDataFromPath($datapool, $pathArray);
            }

            $elementClass->setCurrentConfigElement($element);
            $elementClass->setDataFromExportDataBag($this->exportDataBag);
            $elementClass->render();

            // Iterate over children elements
            if (array_key_exists('children', $element)) {
                $this->processElements($element['children']);
            }

            // Now that all children have been processed, we can remove the structural element from the working path
            if ($elementClass instanceof StructuralElementInterface) {
                $this->exportDataBag->removeFromWorkingPath();
            }

            // recall yourself if iterate is true and there are still entities left in the used datapool
            if (isset($datapool)) {
                $isDatapoolEmpty = $datapool->isEmpty();
                if (array_key_exists('iterate', $element) && $element['iterate'] && !$isDatapoolEmpty) {
                    $datapool->setNextCurrentEntity();
                    $this->processElements([$element]);
                }
            }
        }
    }

    /**
     * @param string $exportName
     * @return array
     * @throws ExportConfigException|Exception
     */
    private function getExportConfig(string $exportName): array
    {
        $exportPath = $this->parameterBag->get('kernel.project_dir'). '/config/exports';
        $fileLocator = new FileLocator($exportPath);
        $exportFiles = $fileLocator->locate('exports.yml', null, false);

        $exportConfig = file_get_contents($exportFiles[0]);
        $parsedConfig = Yaml::parse($exportConfig);

        return $this->processConfiguration($parsedConfig['Exports'], $exportName);
    }

    /**
     * @param array $parsedConfig
     * @param string $exportName
     * @return array
     * @throws ExportConfigException|Exception
     */
    private function processConfiguration(array $parsedConfig, string $exportName): array
    {
        if (!isset($parsedConfig[$exportName])) {
            throw ExportConfigException::exportDefinitionNotFound($exportName);
        }

        $processor= new Processor();
        $exportConfigTreeBuilder = new ExportConfigTreeBuilder();

        $processedConfig = $processor->processConfiguration(
            $exportConfigTreeBuilder,
            [$parsedConfig]
        );

        return $processedConfig[$exportName];
    }

    /**
     * @param Datapool $datapool
     * @param array $pathArray
     * @throws AccessException
     */
    private function setCurrentElementDataFromPath(Datapool $datapool, array $pathArray): void
    {
        if (0 !== count($pathArray)) {
            $currentElementData = $datapool->getDataFromPath($pathArray);
            $this->exportDataBag->setCurrentElementData($currentElementData);
        }
    }
}
