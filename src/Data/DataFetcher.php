<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Data;

use EightDashThree\Querying\ConditionParsers\Drupal\DrupalFilterException;
use EightDashThree\Querying\ConditionParsers\Drupal\DrupalFilterParser;
use EightDashThree\Querying\Contracts\FunctionInterface;
use EightDashThree\Querying\ObjectProviders\TypeRestrictedEntityProvider;
use EightDashThree\Wrapping\Contracts\WrapperFactoryInterface;
use EightDashThree\Wrapping\WrapperFactories\WrapperObject;

class DataFetcher
{
    /**
     * @var array|FunctionInterface
     */
    private $conditions = [];

    private array $sort = [];

    private int $offset = 0;

    private int $limit = 5;

    private array $items = [];

    private bool $continueLoading = false;

    private TypeRestrictedEntityProvider $resourceProvider;

    private string $resourceType;

    private WrapperFactoryInterface $wrapperFactory;

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
        try {
            $this->items = $this->resourceProvider->getObjects([$this->conditions], $this->sort, $this->offset, $this->limit);
        } catch (\Exception $e) {
            $this->items = [];
        }
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
