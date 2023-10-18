<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Recipes;

use DemosEurope\DocumentBakery\Data\DataFetcher;
use DemosEurope\DocumentBakery\Data\DataFetcherFactory;
use DemosEurope\DocumentBakery\Data\RecipeDataBagInterface;
use DemosEurope\DocumentBakery\Data\RecipeWordDataBag;
use DemosEurope\DocumentBakery\Exceptions\DocumentGenerationException;
use DemosEurope\DocumentBakery\Instructions\InstructionFactory;
use DemosEurope\DocumentBakery\Styles\StylesRepository;

class RecipeProcessorFactory
{
    /**
     * @var InstructionFactory
     */
    private $instructionFactory;
    /**
     * @var StylesRepository
     */
    private $stylesRepository;
    /**
     * @var DataFetcherFactory
     */
    private $dataFetcherFactory;

    public function __construct(
        DataFetcherFactory $dataFetcherFactory,
        InstructionFactory $instructionFactory,
        StylesRepository $stylesRepository)
    {
        $this->instructionFactory = $instructionFactory;
        $this->stylesRepository = $stylesRepository;
        $this->dataFetcherFactory = $dataFetcherFactory;
    }

    public function build(RecipeWordDataBag $recipeDataBag): RecipeProcessor
    {
        $dataFetchers = $this->buildDataFetchers($recipeDataBag);

        return new RecipeProcessor($dataFetchers, $this->instructionFactory, $recipeDataBag, $this->stylesRepository);
    }

    /**
     * Prepares the queries to be used to create the DataFetchers.
     *
     * @return array<DataFetcher>
     * @throws DocumentGenerationException
     */
    private function buildDataFetchers(RecipeDataBagInterface $recipeDataBag): array
    {
        $dataFetchers = [];

        $queryVariables = $recipeDataBag->getQueryVariables();
        foreach ($recipeDataBag->getQueries() as $queryName => $queryData) {
            $processedQuery = $this->matchPlaceholdersInQuery($queryData, $queryVariables);
            $dataFetchers[$queryName] = $this->dataFetcherFactory->build($processedQuery);
        }

        return $dataFetchers;
    }

    /**
     * @param array<string, string> $query
     * @return array<string, string>
     * @throws DocumentGenerationException
     */
    private function matchPlaceholdersInQuery(array $query, array $queryVariables): array
    {
        foreach ($query as $key => $value) {
            if (is_array($value)) {
                $query[$key] = $this->matchPlaceholdersInQuery($value, $queryVariables);
            } elseif (is_string($value) && str_contains($value, '{{')) {
                // check if placeholder is present and replace it
                $trimmedPlaceholder = trim($value, '{}');
                if (!array_key_exists($trimmedPlaceholder, $queryVariables)) {
                    throw DocumentGenerationException::noValueForPlaceholder($trimmedPlaceholder);
                }
                $query[$key] = $queryVariables[$trimmedPlaceholder];
            }
        }

        return $query;
    }
}
