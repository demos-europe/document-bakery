<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Data;

use EDT\DqlQuerying\Contracts\ClauseFunctionInterface;

class DataFetcherFactory
{
    public function build(array $parsedQuery, ClauseFunctionInterface $conditions): DataFetcher
    {
        return new DataFetcher($parsedQuery, $conditions);
    }
}
