<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery;

use DemosEurope\DocumentBakery\Recipes\RecipeConfigTreeBuilder;
use DemosEurope\DocumentBakery\Data\Datapool;
use DemosEurope\DocumentBakery\Data\DatapoolManager;
use DemosEurope\DocumentBakery\Data\ExportDataBag;
use DemosEurope\DocumentBakery\Elements\ElementFactory;
use DemosEurope\DocumentBakery\Elements\StructuralElementInterface;
use DemosEurope\DocumentBakery\Exceptions\ExportConfigException;
use DemosEurope\DocumentBakery\Exceptions\ExportGenerationException;
use DemosEurope\DocumentBakery\Recipes\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use EightDashThree\Querying\ConditionParsers\Drupal\DrupalFilterParser;
use EightDashThree\Wrapping\Contracts\AccessException;
use EightDashThree\Wrapping\TypeProviders\PrefilledTypeProvider;
use Exception;
use PhpOffice\PhpWord\Element\AbstractElement;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Writer\WriterInterface;
use Symfony\Component\Config\Definition\Processor;

class Exporter
{
    /** @var ElementFactory */
    private $elementFactory;

    /** @var AbstractElement */
    public $currentStructureElement;

    /** @var ExportDataBag */
    private $exportDataBag;

    /** @var DatapoolManager */
    private $datapoolManager;

    /** @var DrupalFilterParser */
    private $drupalFilterParser;

    /** @var RecipeRepository  */
    private $recipeRepository;
    /** @var EntityManagerInterface  */
    private $entityManager;
    /** @var PrefilledTypeProvider  */
    private $prefilledTypeProvider;

    public function __construct(
        ElementFactory $elementFactory,
        EntityManagerInterface $entityManager,
        DrupalFilterParser $drupalFilterParser,
        RecipeRepository $recipeRepository,
        PrefilledTypeProvider $prefilledTypeProvider
    )
    {
        $this->elementFactory = $elementFactory;
        $this->exportDataBag = new ExportDataBag();
        $this->drupalFilterParser = $drupalFilterParser;
        $this->recipeRepository = $recipeRepository;
        $this->entityManager = $entityManager;
        $this->prefilledTypeProvider = $prefilledTypeProvider;
    }

    /**
     * @throws ExportGenerationException
     * @throws \PhpOffice\PhpWord\Exception\Exception
     * @throws AccessException
     */
    public function create(string $exportName, array $queryVariables): ?WriterInterface
    {
        $exportConfig = $this->recipeRepository->get($exportName);

        $this->datapoolManager = new DatapoolManager(
            $exportConfig['queries'],
            $queryVariables,
            $this->entityManager,
            $this->drupalFilterParser,
            $this->prefilledTypeProvider
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
        $exportConfigTreeBuilder = new RecipeConfigTreeBuilder();

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
