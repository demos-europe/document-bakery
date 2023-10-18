<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Data;

use EDT\JsonApi\ResourceTypes\ReadableTypeInterface;
use EDT\Querying\Contracts\FunctionInterface;
use EDT\Querying\Contracts\PathsBasedInterface;
use EDT\Querying\Pagination\PagePagination;
use EDT\Wrapping\Contracts\AccessException;
use EDT\Wrapping\WrapperFactories\WrapperObject;
use Pagerfanta\Pagerfanta;

class DataFetcher
{
    /**
     * @var array|FunctionInterface
     */
    private $conditions;

    private $sort;

    private $paginationNumber = 1;

    private $paginationSize = 5;

    private $items = [];

    private $continueLoading = false;

    /**
     * @var WrapperObject
     */
    private $currentEntity;

    private $currentIterationNumber = 0;

    /**
     * @var ReadableTypeInterface|mixed
     */
    private $resourceType;

    public function __construct(
        ReadableTypeInterface $resourceType,
        PathsBasedInterface $filterConditions,
        array $sortCondition,
        int $paginationSize,
        bool $isIterable
    ) {
        $this->conditions = $filterConditions;
        $this->resourceType = $resourceType;
        $this->paginationSize = $paginationSize;
        $this->sort = $sortCondition;
        $this->loadNextChunkOfItems();
        if ($isIterable) {
            $this->setContinueLoading(true);
        }
        $this->setNextCurrentEntity();
        $this->currentIterationNumber++;
    }

    private function getNextItem(): WrapperObject
    {
        $currentEntity = new WrapperObject(array_shift($this->items), $this->resourceType);
        if ($this->continueLoading && 0 === count($this->items)) {
            $this->loadNextChunkOfItems();
        }

        return $currentEntity;
    }

    private function setContinueLoading(bool $value): void
    {
        $this->continueLoading = $value;
    }

    private function loadNextChunkOfItems(): void
    {
        try {
            /** @var Pagerfanta $paginatedResults */
            $paginatedResults = $this->resourceType->getEntitiesForPage([$this->conditions], $this->sort, new PagePagination($this->paginationSize, $this->paginationNumber));
            $this->items = iterator_to_array($paginatedResults->getCurrentPageResults(), true);
        } catch (\Exception $e) {
            $this->items = [];
        }
        // Always increase the offset to not load data twice
        $this->paginationNumber++;
        // If less items than the limit were loaded, it means we reached the end. So we don't need to load again
        if (count($this->items) < $this->paginationSize) {
            $this->setContinueLoading(false);
        }
    }

    private function getItemCount(): int
    {
        return count($this->items);
    }

    public function setNextCurrentEntity(): void
    {
        $this->currentEntity = $this->getNextItem();
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
        return 0 === $this->getItemCount();
    }

}
