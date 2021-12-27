<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Data;

use DemosEurope\DocumentBakery\Exceptions\DocumentGenerationException;

class DatapoolManager
{
    private array $datapools = [];
    private array $queries;
    private array $queryVariables;
    private DataFetcherFactory $dataFetcherFactory;

    public function __construct(
        array $queries,
        array $queryVariables,
        DataFetcherFactory $dataFetcherFactory
    )
    {
        $this->queries = $queries;
        $this->queryVariables = $queryVariables;
        $this->dataFetcherFactory = $dataFetcherFactory;
    }

    /**
     * @throws DocumentGenerationException
     */
    public function create(string $name): void
    {
        $query = $this->queries[$name];
        $parsedQuery = $this->parseQuery($query);
        $dataFetcher = $this->dataFetcherFactory->build($parsedQuery);
        $this->datapools[$name] = new Datapool($dataFetcher);
    }

    /**
     * @return array<int, mixed>
     * @throws DocumentGenerationException
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
                if (!array_key_exists($trimmedPlaceholder, $this->queryVariables)) {
                    throw DocumentGenerationException::noValueForPlaceholder($trimmedPlaceholder);
                }
                $query[$key] = $this->queryVariables[$trimmedPlaceholder];
            }
        }

        return $query;
    }
}
