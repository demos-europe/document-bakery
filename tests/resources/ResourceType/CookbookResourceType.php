<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Tests\resources\ResourceType;


use DemosEurope\DocumentBakery\Tests\resources\Entity\Cookbook;
use EightDashThree\DqlQuerying\ConditionFactories\DqlConditionFactory;
use EightDashThree\Querying\Contracts\FunctionInterface;
use EightDashThree\Wrapping\Contracts\Types\ReadableTypeInterface;

class CookbookResourceType implements ReadableTypeInterface
{
    /**
     * @var DqlConditionFactory
     */
    private $dqlConditionFactory;

    public function __construct(DqlConditionFactory $dqlConditionFactory)
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
}
