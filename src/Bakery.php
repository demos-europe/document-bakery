<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery;

use DemosEurope\DocumentBakery\Data\DatapoolManager;
use DemosEurope\DocumentBakery\Data\RecipeDataBag;
use DemosEurope\DocumentBakery\Exceptions\DocumentGenerationException;
use DemosEurope\DocumentBakery\Recipes\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use EightDashThree\Querying\ConditionParsers\Drupal\DrupalFilterParser;
use EightDashThree\Wrapping\Contracts\AccessException;
use EightDashThree\Wrapping\TypeProviders\PrefilledTypeProvider;
use PhpOffice\PhpWord\Writer\WriterInterface;

class Bakery
{
    private DrupalFilterParser $drupalFilterParser;

    private RecipeRepository $recipeRepository;

    private EntityManagerInterface $entityManager;

    private PrefilledTypeProvider $prefilledTypeProvider;

    private RecipeProcessorFactory $recipeProcessorFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        DrupalFilterParser     $drupalFilterParser,
        RecipeProcessorFactory $recipeProcessorFactory,
        RecipeRepository       $recipeRepository,
        PrefilledTypeProvider  $prefilledTypeProvider
    )
    {
        $this->drupalFilterParser = $drupalFilterParser;
        $this->recipeRepository = $recipeRepository;
        $this->entityManager = $entityManager;
        $this->prefilledTypeProvider = $prefilledTypeProvider;
        $this->recipeProcessorFactory = $recipeProcessorFactory;
    }

    /**
     * @param array<string, string> $queryVariables
     * @throws DocumentGenerationException
     * @throws AccessException|Exceptions\StyleException
     */
    public function create(string $recipeName, array $queryVariables): ?WriterInterface
    {
        $recipeConfig = $this->recipeRepository->get($recipeName);

        $recipeDataBag = $this->getRecipeDataBag($recipeConfig);
        $datapoolManager = new DatapoolManager(
            $recipeConfig['queries'],
            $queryVariables,
            $this->entityManager,
            $this->drupalFilterParser,
            $this->prefilledTypeProvider
        );

        $recipeProcessor = $this->recipeProcessorFactory->build($datapoolManager, $recipeDataBag);

        return $recipeProcessor->createFromRecipe();
    }

    /**
     * @param array<string, mixed> $recipeConfig
     */
    private function getRecipeDataBag(array $recipeConfig): RecipeDataBag
    {
        $recipeDataBag = new RecipeDataBag();
        if (isset($recipeConfig['format'])) {
            $recipeDataBag->setFormat($recipeConfig['format']);
        }
        if (isset($recipeConfig['styles']) && 0 < count($recipeConfig['styles'])) {
            $recipeDataBag->setStyles($recipeConfig['styles']);
        }
        $recipeDataBag->setInstructions($recipeConfig['instructions']);

        return $recipeDataBag;
    }
}
