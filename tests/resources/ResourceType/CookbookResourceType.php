<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Tests\resources\ResourceType;


use DemosEurope\DocumentBakery\Tests\resources\Entity\Cookbook;
use EightDashThree\Querying\ConditionFactories\PhpConditionFactory;
use EightDashThree\Querying\Contracts\FunctionInterface;
use EightDashThree\Wrapping\Contracts\Types\FilterableTypeInterface;
use EightDashThree\Wrapping\Contracts\Types\ReadableTypeInterface;

class CookbookResourceType implements ReadableTypeInterface, FilterableTypeInterface
{
    /**
     * @var PhpConditionFactory
     */
    private $dqlConditionFactory;

    public function __construct(PhpConditionFactory $dqlConditionFactory)
    {
        $this->dqlConditionFactory = $dqlConditionFactory;
    }

    public function getReadableProperties(): array
    {
        return [
            'id' => null,
            'name' => null,
            'flavour' => null,
        ];
    }

    public function getEntityClass(): string
    {
        return Cookbook::class;
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function isReferencable(): bool
    {
        return false;
    }

    public function isDirectlyAccessible(): bool
    {
        return true;
    }

    public function getAccessCondition(): FunctionInterface
    {
        return $this->dqlConditionFactory->true();
    }

    public function getAliases(): array
    {
        return [];
    }

    public function getInternalProperties(): array
    {
        return [];
    }

    public function getDefaultSortMethods(): array
    {
        return [];
    }

    public function getFilterableProperties(): array
    {
        return [
            'id' => null,
            'name' => null,
            'flavour' => null,
        ];
    }
}
