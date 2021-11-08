<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Data;

use EightDashThree\Querying\ConditionParsers\Drupal\DrupalFilterException;
use EightDashThree\Querying\ConditionParsers\Drupal\DrupalFilterParser;
use EightDashThree\Querying\ObjectProviders\TypeRestrictedEntityProvider;
use EightDashThree\Wrapping\Contracts\WrapperFactoryInterface;
use EightDashThree\Wrapping\WrapperFactories\WrapperObject;

class DataFetcher
{
    private $conditions = [];

    private $sort = [];

    private $offset = 0;

    private $limit = 5;

    private $items = [];

    private $continueLoading = false;

    private $resourceProvider;

    private $resourceType;

    /** @var WrapperFactoryInterface  */
    private $wrapperFactory;

    public function __construct(
        array $query,
        TypeRestrictedEntityProvider $objectProvider,
        WrapperFactoryInterface $wrapperFactory,
        DrupalFilterParser $drupalFilterParser
    ) {
        try {
            $this->conditions = $drupalFilterParser->createRootFromArray($query['filter']);
        } catch (DrupalFilterException $e) {
            $this->conditions = [];
        }
        $this->resourceType = $query['resource_type'];
        $this->resourceProvider = $objectProvider;
        $this->loadNextChunkOfItems();
        if (array_key_exists('iterable', $query) && true === $query['iterable']) {
            $this->setContinueLoading(true);
        }
        $this->wrapperFactory = $wrapperFactory;
    }

    public function getNextItem(): WrapperObject
    {
        $currentEntity = $this->wrapperFactory->createWrapper(array_shift($this->items), $this->resourceType);
        if ($this->continueLoading && 0 === count($this->items)) {
            $this->loadNextChunkOfItems();
        }

        return $currentEntity;
    }

    public function setContinueLoading(bool $value): void
    {
        $this->continueLoading = $value;
    }

    public function loadNextChunkOfItems(): void
    {
        $this->items = $this->resourceProvider->getObjects([$this->conditions], $this->sort, $this->offset, $this->limit);
        // Always increase the offset to not load data twice
        $this->offset += $this->limit;
        // If less items than the limit were loaded, it means we reached the end. So we don't need to load again
        if (count($this->items) < $this->limit) {
            $this->setContinueLoading(false);
        }
    }

    public function getItemCount(): int
    {
        return count($this->items);
    }

}