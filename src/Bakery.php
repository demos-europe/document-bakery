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
    /** @var InstructionFactory */
    private $instructionFactory;

    /** @var DrupalFilterParser */
    private $drupalFilterParser;

    /** @var RecipeRepository  */
    private $recipeRepository;
    /** @var EntityManagerInterface  */
    private $entityManager;
    /** @var PrefilledTypeProvider  */
    private $prefilledTypeProvider;
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
     * @throws DocumentGenerationException
     * @throws AccessException
     */
    public function create(string $recipeName, array $queryVariables): ?WriterInterface
    {
        $recipeConfig = $this->recipeRepository->get($recipeName);

        $recipeDataBag = new RecipeDataBag();
        $datapoolManager = new DatapoolManager(
            $recipeConfig['queries'],
            $queryVariables,
            $this->entityManager,
            $this->drupalFilterParser,
            $this->prefilledTypeProvider
        );
        if (isset($recipeConfig['format'])) {
            $recipeDataBag->setFormat($recipeConfig['format']);
        }
        if (isset($recipeConfig['styles']) && 0 < count($recipeConfig['styles'])) {
            $this->stylesRepository->mergeStyles($recipeConfig['styles']);
        }
        $recipeDataBag->setStylesRepository($this->stylesRepository);
        $recipeDataBag->setInstructions($recipeConfig['instructions']);

        $recipeProcessor = new RecipeProcessor($datapoolManager, $this->instructionFactory, $recipeDataBag);

        return $recipeProcessor->createFromRecipe();
    }
}
