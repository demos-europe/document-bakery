<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery;

use DemosEurope\DocumentBakery\Data\DatapoolManager;
use DemosEurope\DocumentBakery\Data\RecipeDataBag;
use DemosEurope\DocumentBakery\Instructions\InstructionFactory;
use DemosEurope\DocumentBakery\Exceptions\DocumentGenerationException;
use DemosEurope\DocumentBakery\Recipes\RecipeRepository;
use DemosEurope\DocumentBakery\Styles\StylesRepository;
use Doctrine\ORM\EntityManagerInterface;
use EightDashThree\Querying\ConditionParsers\Drupal\DrupalFilterParser;
use EightDashThree\Wrapping\Contracts\AccessException;
use EightDashThree\Wrapping\TypeProviders\PrefilledTypeProvider;
use PhpOffice\PhpWord\Writer\WriterInterface;

class Bakery
{
    private InstructionFactory $instructionFactory;

    private DrupalFilterParser $drupalFilterParser;

    private RecipeRepository $recipeRepository;

    private EntityManagerInterface $entityManager;

    private PrefilledTypeProvider $prefilledTypeProvider;

    private StylesRepository $stylesRepository;

    public function __construct(
        InstructionFactory     $instructionFactory,
        EntityManagerInterface $entityManager,
        DrupalFilterParser     $drupalFilterParser,
        RecipeRepository       $recipeRepository,
        PrefilledTypeProvider  $prefilledTypeProvider,
        StylesRepository        $stylesRepository
    )
    {
        $this->instructionFactory = $instructionFactory;
        $this->drupalFilterParser = $drupalFilterParser;
        $this->recipeRepository = $recipeRepository;
        $this->entityManager = $entityManager;
        $this->prefilledTypeProvider = $prefilledTypeProvider;
        $this->stylesRepository = $stylesRepository;
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

        $recipeProcessor = new RecipeProcessor($datapoolManager, $this->instructionFactory, $recipeDataBag);

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
            $this->stylesRepository->mergeStyles($recipeConfig['styles']);
        }
        $recipeDataBag->setStylesRepository($this->stylesRepository);
        $recipeDataBag->setInstructions($recipeConfig['instructions']);

        return $recipeDataBag;
    }
}
