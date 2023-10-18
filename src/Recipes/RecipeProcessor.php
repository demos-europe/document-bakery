<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Recipes;

use DemosEurope\DocumentBakery\Data\DataFetcher;
use DemosEurope\DocumentBakery\Data\DataFetcherFactory;
use DemosEurope\DocumentBakery\Data\RecipeWordDataBag;
use DemosEurope\DocumentBakery\Exceptions\DocumentGenerationException;
use DemosEurope\DocumentBakery\Exceptions\StyleException;
use DemosEurope\DocumentBakery\Instructions\InstructionFactory;
use DemosEurope\DocumentBakery\Instructions\StructuralInstructionInterface;
use DemosEurope\DocumentBakery\Mapper\PhpWordStyleOptions;
use DemosEurope\DocumentBakery\Styles\StylesRepository;
use EDT\Wrapping\Contracts\AccessException;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Writer\WriterInterface;

class RecipeProcessor
{
    /**
     * @var RecipeWordDataBag
     */
    private $recipeDataBag;

    /**
     * @var InstructionFactory
     */
    private $instructionFactory;

    /**
     * @var StylesRepository
     */
    private $stylesRepository;

    /**
     * @var DataFetcher[]
     */
    private $dataProviders = [];
    /**
     * @var DataFetcherFactory
     */
    private $dataFetcherFactory;

    public function __construct(
        DataFetcherFactory $dataFetcherFactory,
        InstructionFactory $instructionFactory,
        RecipeWordDataBag  $recipeDataBag,
        StylesRepository   $stylesRepository
    )
    {
        $this->recipeDataBag = $recipeDataBag;
        $this->instructionFactory = $instructionFactory;
        $this->stylesRepository = $stylesRepository;
        $this->dataFetcherFactory = $dataFetcherFactory;
    }

    /**
     * @throws DocumentGenerationException|StyleException
     */
    public function createFromRecipe (): ?WriterInterface
    {
        // Process Styles
        $styles = $this->recipeDataBag->getStyles();
        if (0 < count($styles)) {
            $this->stylesRepository->mergeStyles($styles);
        }

        $this->processInstructions($this->recipeDataBag->getInstructions());

        try {
            $writerObject = IOFactory::createWriter($this->recipeDataBag->getWriterObject(), 'Word2007');
        } catch (\Exception $e) {
            throw DocumentGenerationException::writerObjectGenerationFailed();
        }
        return $writerObject;
    }

    /**
     * @param array<string, mixed> $instructions
     * @throws DocumentGenerationException|AccessException|StyleException
     */
    private function processInstructions(array $instructions): void
    {
        foreach ($instructions as $instruction) {
            $instructionClass = $this->instructionFactory->lookupForName($instruction['name']);

            // handle data lookup
            if (array_key_exists('path', $instruction)) {
                $instructionDataPath = explode('.', $instruction['path']);
                $this->setCurrentInstructionDataFromPath($instructionDataPath);
            }

            $mappedStyles = $this->getMappedStyleContent($instruction);
            $instructionClass->initializeInstruction($instruction, $this->recipeDataBag, $mappedStyles);
            $instructionClass->render();

            // Iterate over children instructions
            if (array_key_exists('children', $instruction)) {
                $this->processInstructions($instruction['children']);
            }

            // Now that all children have been processed, we can remove the structural instruction from the working path
            if ($instructionClass instanceof StructuralInstructionInterface) {
                $this->recipeDataBag->removeFromWorkingPath();
            }

            // recall yourself if iterate is true and there are still entities left in the used dataFetcher
            if (isset($instructionDataPath)) {
                $dataFetcher = $this->dataProviders[$instructionDataPath[0]];
                $isDataFetcherEmpty = $dataFetcher->isEmpty();
                if (array_key_exists('iterate', $instruction) && $instruction['iterate'] && !$isDataFetcherEmpty) {
                    $dataFetcher->setNextCurrentEntity();
                    $this->processInstructions([$instruction]);
                }
            }
        }
    }

    /**
     * @param array<int, string> $instructionDataPath
     * @throws AccessException|DocumentGenerationException
     */
    private function setCurrentInstructionDataFromPath(array $instructionDataPath): void
    {
        $dataFetcherName = array_shift($instructionDataPath);
        $this->createDataFetcherIfNotExists($dataFetcherName);
        if (0 !== count($instructionDataPath)) {
            $currentInstructionData = $this->dataProviders[$dataFetcherName]->getDataFromPath($instructionDataPath);
            $this->recipeDataBag->setCurrentInstructionData($currentInstructionData);
        }
    }

    /**
     * @throws StyleException
     */
    private function getMappedStyleContent(array $instruction): array
    {
        $styleContent = [];
        if (isset($instruction['style']) && 0 < count($instruction['style'])) {
            // get attributes of style
            if (isset($instruction['style']['name'])) {
                $style = $this->stylesRepository->get($instruction['style']['name']);
                $styleContent = $style['attributes'];
            }
            // get local style attributes and merge them into existing styles
            if (isset($instruction['style']['attributes'])) {
                $styleContent = array_replace_recursive($styleContent, $instruction['style']['attributes']);
            }
        }

        // Now we need to map the attributes to the possible phpWord style sets
        return PhpWordStyleOptions::getMappedStyleOptions($styleContent);
    }

    /**
     * @throws DocumentGenerationException
     */
    private function createDataFetcher(string $name): void
    {
        $query = $this->recipeDataBag->getQueryByName($name);
        $parsedQuery = $this->parseQuery($query);
        $dataFetcher = $this->dataFetcherFactory->build($parsedQuery);
        $this->dataProviders[$name] = $dataFetcher;
    }

    /**
     * @throws DocumentGenerationException
     */
    private function createDataFetcherIfNotExists(string $dataFetcherName): void
    {
        if (!array_key_exists($dataFetcherName, $this->dataProviders)) {
            $this->createDataFetcher($dataFetcherName);
        }
    }

    /**
     * @param array<string, string> $query
     * @return array<string, string>
     * @throws DocumentGenerationException
     */
    private function parseQuery(array $query): array
    {
        foreach ($query as $key => $value) {
            if (is_array($value)) {
                $query[$key] = $this->parseQuery($value);
            } elseif (is_string($value) && false !== strpos($value, '{{')) {
                // check if placeholder is present and replace it
                $trimmedPlaceholder = trim($value, '{}');
                $queryVariables = $this->recipeDataBag->getQueryVariables();
                if (!array_key_exists($trimmedPlaceholder, $queryVariables)) {
                    throw DocumentGenerationException::noValueForPlaceholder($trimmedPlaceholder);
                }
                $query[$key] = $queryVariables[$trimmedPlaceholder];
            }
        }

        return $query;
    }

}
