<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Data;

use EDT\ConditionFactory\ConditionFactoryInterface;
use EDT\ConditionFactory\ConditionGroupFactoryInterface;
use EDT\Querying\ConditionParsers\Drupal\DrupalFilterParser;
use EDT\Querying\Contracts\PathException;
use EDT\Querying\Contracts\PathsBasedInterface;
use EDT\Querying\Contracts\SortMethodFactoryInterface;

/**
 * Builds Filters and Sorts based on the given query and creates DataFetchers with that.
 */
class DataFetcherFactory
{
    private DrupalFilterParser $drupalFilterParser;
    private SortMethodFactoryInterface $sortMethodFactory;
    private ConditionGroupFactoryInterface $conditionFactory;

    public function __construct(ConditionFactoryInterface&ConditionGroupFactoryInterface $conditionFactory, DrupalFilterParser $drupalFilterParser,  SortMethodFactoryInterface $sortMethodFactory)
    {
        $this->drupalFilterParser = $drupalFilterParser;
        $this->sortMethodFactory = $sortMethodFactory;
        $this->conditionFactory = $conditionFactory;
    }

    public function build(array $parsedQuery): DataFetcher
    {
        $resourceType = $parsedQuery['resource_type'];
        $filterCondition = $this->buildFilterConditionClause($parsedQuery['filter'] ?? []);
        $sortCondition = $this->buildSortClause($parsedQuery['sort'] ?? []);
        $paginationSize = $parsedQuery['pagination_size'] ?? 5;
        $isIterable = $parsedQuery['iterable'] ?? false;

        return new DataFetcher($resourceType, $filterCondition, $sortCondition, $paginationSize, $isIterable);
    }


    private function buildFilterConditionClause(array $filterConditions): PathsBasedInterface
    {
        $drupalParsedConditions = $this->drupalFilterParser->parseFilter($filterConditions);
        if (count($drupalParsedConditions) === 0) {
            return $this->conditionFactory->true();
        }

        return $this->conditionFactory->allConditionsApply($drupalParsedConditions);
    }

    /**
     * @param array<string, string> $sortCondition
     * @return array<null|PathsBasedInterface>
     * @throws PathException
     */
    private function buildSortClause(array $sortCondition): array
    {
        if (!array_key_exists('direction', $sortCondition)) {
            return [];
        }

        if ($sortCondition['direction'] === 'desc') {
            return [$this->sortMethodFactory->propertyDescending($sortCondition['property'])];
        }

        return [$this->sortMethodFactory->propertyAscending($sortCondition['property'])];
    }
}
