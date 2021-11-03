<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery;

use DemosEurope\DocumentBakery\Exceptions\ExportGenerationException;
use DemosEurope\DocumentBakery\TemporaryStuff\EntityFetcher;
use EightDashThree\Querying\ConditionParsers\Drupal\DrupalFilterParser;
use EightDashThree\Wrapping\Utilities\GenericEntityFetcher;

class DatapoolManager
{
    /**
     * @var array
     */
    private $datapools = [];

    private $queries;

    private $queryVariables;
    /**
     * @var EntityFetcher
     */
    private $entityFetcher;
    /**
     * @var DrupalFilterParser
     */
    private $drupalFilterParser;

    public function __construct(
        array $queries,
        array $queryVariables,
        EntityFetcher $entityFetcher,
        DrupalFilterParser $drupalFilterParser)
    {
        $this->queries = $queries;
        $this->queryVariables = $queryVariables;
        $this->entityFetcher = $entityFetcher;
        $this->drupalFilterParser = $drupalFilterParser;
    }

    /**
     * @param string $name
     * @throws ExportGenerationException
     */
    public function create(string $name): void
    {
        $query = $this->queries[$name];
        $parsedQuery = $this->parseQuery($query);
        $dataFetcher = $this->getDatafetcher($parsedQuery);
        $this->datapools[$name] = new Datapool($dataFetcher);
    }

    private function getDataFetcher($parsedQuery): DataFetcher
    {
        return new DataFetcher($parsedQuery, $this->entityFetcher, $this->drupalFilterParser);
    }

    /**
     * @param string $path
     * @return array
     * @throws ExportGenerationException
     */
    public function parsePath(string $path): array
    {
        $pathArray = explode('.', $path);
        $datapoolName = array_shift($pathArray);
        // if datapool does not yet exist
        if (!array_key_exists($datapoolName, $this->datapools)) {
            $this->create($datapoolName);
        }

        $datapool = $this->datapools[$datapoolName];

        return [$datapool, $pathArray];
    }

    /**
     * @param array $query
     * @return array
     * @throws ExportGenerationException
     */
    private function parseQuery(array $query): array
    {
        foreach ($query as $key => $value) {
            if (is_array($value)) {
                $query[$key] = $this->parseQuery($value);
            } elseif (is_string($value) && false !== strpos($value, '{{')) {
                // check if placeholder is present and replace it
                $trimmedPlaceholder = trim($value, '{}');
                if (!array_key_exists($trimmedPlaceholder, $this->queryVariables)) {
                    throw ExportGenerationException::noValueForPlaceholder($trimmedPlaceholder);
                }
                $query[$key] = $this->queryVariables[$trimmedPlaceholder];
            }
        }

        return $query;
    }
}
