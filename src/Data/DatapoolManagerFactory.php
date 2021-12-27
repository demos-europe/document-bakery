<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Data;

use Doctrine\ORM\EntityManagerInterface;
use EightDashThree\Querying\ConditionParsers\Drupal\DrupalFilterParser;
use EightDashThree\Wrapping\TypeProviders\PrefilledTypeProvider;

class DatapoolManagerFactory
{
    private DrupalFilterParser $drupalFilterParser;
    private EntityManagerInterface $entityManager;
    private PrefilledTypeProvider $prefilledTypeProvider;

    public function __construct(
        DrupalFilterParser     $drupalFilterParser,
        EntityManagerInterface $entityManager,
        PrefilledTypeProvider  $prefilledTypeProvider
    )
    {
        $this->drupalFilterParser = $drupalFilterParser;
        $this->entityManager = $entityManager;
        $this->prefilledTypeProvider = $prefilledTypeProvider;
    }

    public function build(array $queries, array $queryVariables): DatapoolManager
    {
        return new DatapoolManager(
            $queries,
            $queryVariables,
            $this->entityManager,
            $this->drupalFilterParser,
            $this->prefilledTypeProvider
        );
    }
}
