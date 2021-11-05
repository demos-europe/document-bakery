<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Data;

use DemosEurope\DocumentBakery\Exceptions\ExportGenerationException;
use Doctrine\ORM\EntityManagerInterface;
use EightDashThree\DqlQuerying\ObjectProviders\DoctrineOrmEntityProvider;
use EightDashThree\DqlQuerying\PropertyAccessors\ProxyPropertyAccessor;
use EightDashThree\Querying\ConditionParsers\Drupal\DrupalFilterParser;
use EightDashThree\Querying\ObjectProviders\TypeRestrictedEntityProvider;
use EightDashThree\Querying\Utilities\ConditionEvaluator;
use EightDashThree\Wrapping\Contracts\WrapperFactoryInterface;
use EightDashThree\Wrapping\TypeProviders\PrefilledTypeProvider;
use EightDashThree\Wrapping\Utilities\PropertyReader;
use EightDashThree\Wrapping\Utilities\SchemaPathProcessor;
use EightDashThree\Wrapping\Utilities\TypeAccessor;
use EightDashThree\Wrapping\WrapperFactories\WrapperObjectFactory;

class DatapoolManager
{
    /**
     * @var array
     */
    private $datapools = [];

    private $queries;

    private $queryVariables;
    /**
     * @var DrupalFilterParser
     */
    private $drupalFilterParser;

    /** @var WrapperFactoryInterface */
    private $wrapperFactory;
    private EntityManagerInterface $entityManager;
    private PrefilledTypeProvider $prefilledTypeProvider;

    public function __construct(
        array $queries,
        array $queryVariables,
        EntityManagerInterface $entityManager,
        DrupalFilterParser $drupalFilterParser,
        PrefilledTypeProvider $prefilledTypeProvider
    )
    {
        $this->queries = $queries;
        $this->queryVariables = $queryVariables;
        $this->drupalFilterParser = $drupalFilterParser;
        $this->entityManager = $entityManager;
        $this->prefilledTypeProvider = $prefilledTypeProvider;
        $this->buildWrapperFactory();
    }

    /**
     * @param string $name
     * @throws ExportGenerationException
     */
    public function create(string $name): void
    {
        $query = $this->queries[$name];
        $parsedQuery = $this->parseQuery($query);
        $dataFetcher = $this->getDatafetcher($parsedQuery);
        $this->datapools[$name] = new Datapool($dataFetcher);
    }

    private function getDataFetcher($parsedQuery): DataFetcher
    {
        $objectProvider = $this->getResourceProvider($parsedQuery['resource_type']);
        return new DataFetcher($parsedQuery, $objectProvider, $this->wrapperFactory, $this->drupalFilterParser);
    }

    /**
     * @param string $path
     * @return array
     * @throws ExportGenerationException
     */
    public function parsePath(string $path): array
    {
        $pathArray = explode('.', $path);
        $datapoolName = array_shift($pathArray);
        // if datapool does not yet exist
        if (!array_key_exists($datapoolName, $this->datapools)) {
            $this->create($datapoolName);
        }

        $datapool = $this->datapools[$datapoolName];

        return [$datapool, $pathArray];
    }

    private function getResourceProvider($ResourceType): TypeRestrictedEntityProvider
    {
        $doctrineProvider = new DoctrineOrmEntityProvider(
            $ResourceType->getEntityClass(),
            $this->entityManager
        );

        $schemaProcessor = new SchemaPathProcessor($this->prefilledTypeProvider);

        return new TypeRestrictedEntityProvider(
            $doctrineProvider,
            $ResourceType,
            $schemaProcessor
        );
    }

    private function buildWrapperFactory(): void
    {
        $propertyAccessor = new ProxyPropertyAccessor($this->entityManager);
        $schemaProcessor = new SchemaPathProcessor($this->prefilledTypeProvider);
        $this->wrapperFactory = new WrapperObjectFactory(
            new TypeAccessor($this->prefilledTypeProvider),
            new PropertyReader($propertyAccessor, $schemaProcessor),
            $propertyAccessor,
            new ConditionEvaluator($propertyAccessor)
        );
    }

    /**
     * @param array $query
     * @return array
     * @throws ExportGenerationException
     */
    private function parseQuery(array $query): array
    {
        foreach ($query as $key => $value) {
            if (is_array($value)) {
                $query[$key] = $this->parseQuery($value);
            } elseif (is_string($value) && false !== strpos($value, '{{')) {
                // check if placeholder is present and replace it
                $trimmedPlaceholder = trim($value, '{}');
                if (!array_key_exists($trimmedPlaceholder, $this->queryVariables)) {
                    throw ExportGenerationException::noValueForPlaceholder($trimmedPlaceholder);
                }
                $query[$key] = $this->queryVariables[$trimmedPlaceholder];
            }
        }

        return $query;
    }
}
