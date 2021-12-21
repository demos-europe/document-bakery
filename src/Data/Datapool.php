<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Data;

use EightDashThree\Wrapping\Contracts\AccessException;
use EightDashThree\Wrapping\WrapperFactories\WrapperObject;

class Datapool
{
    private WrapperObject $currentEntity;

    private int $currentIterationNumber = 0;

    private DataFetcher $dataFetcher;

    public function __construct(DataFetcher $dataFetcher)
    {
        $this->dataFetcher = $dataFetcher;
        $this->setNextCurrentEntity();
        $this->currentIterationNumber++;
    }

    public function setNextCurrentEntity(): void
    {
        $this->currentEntity = $this->dataFetcher->getNextItem();
        $this->currentIterationNumber++;
    }

    /**
     * @param array<int, string> $path
     * @return WrapperObject|mixed|null
     * @throws AccessException
     */
    public function getDataFromPath(array $path)
    {
        $currentData = $this->currentEntity;
        foreach ($path as $property) {
            $currentData = $currentData->__get($property);
        }
        return $currentData;
    }

    public function isEmpty(): bool
    {
        return 0 === $this->dataFetcher->getItemCount();
    }
}
