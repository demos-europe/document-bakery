<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Data;


class DatapoolManagerFactory
{
    private DataFetcherFactory $dataFetcherFactory;

    public function __construct(
        DataFetcherFactory $dataFetcherFactory
    )
    {
        $this->dataFetcherFactory = $dataFetcherFactory;
    }

    public function build(array $queries, array $queryVariables): DatapoolManager
    {
        return new DatapoolManager(
            $queries,
            $queryVariables,
            $this->dataFetcherFactory
        );
    }
}
