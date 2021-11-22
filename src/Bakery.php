<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery;

use DemosEurope\DocumentBakery\Data\Datapool;
use DemosEurope\DocumentBakery\Data\DatapoolManager;
use DemosEurope\DocumentBakery\Data\RecipeDataBag;
use DemosEurope\DocumentBakery\Instructions\InstructionFactory;
use DemosEurope\DocumentBakery\Instructions\StructuralInstructionInterface;
use DemosEurope\DocumentBakery\Exceptions\DocumentGenerationException;
use DemosEurope\DocumentBakery\Recipes\RecipeRepository;
use DemosEurope\DocumentBakery\Styles\StylesRepository;
use Doctrine\ORM\EntityManagerInterface;
use EightDashThree\Querying\ConditionParsers\Drupal\DrupalFilterParser;
use EightDashThree\Wrapping\Contracts\AccessException;
use EightDashThree\Wrapping\TypeProviders\PrefilledTypeProvider;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Writer\WriterInterface;

class Bakery
{
    /** @var InstructionFactory */
    private $instructionFactory;

    /** @var RecipeDataBag */
    private $recipeDataBag;

    /** @var DatapoolManager */
    private $datapoolManager;

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
        $this->recipeDataBag = new RecipeDataBag();
        $this->drupalFilterParser = $drupalFilterParser;
        $this->recipeRepository = $recipeRepository;
        $this->entityManager = $entityManager;
        $this->prefilledTypeProvider = $prefilledTypeProvider;
        $this->stylesRepository = $stylesRepository;
    }

    /**
     * @throws DocumentGenerationException
     * @throws \PhpOffice\PhpWord\Exception\Exception
     * @throws AccessException
     */
    public function create(string $recipeName, array $queryVariables): ?WriterInterface
    {
        $recipeConfig = $this->recipeRepository->get($recipeName);

        $this->datapoolManager = new DatapoolManager(
            $recipeConfig['queries'],
            $queryVariables,
            $this->entityManager,
            $this->drupalFilterParser,
            $this->prefilledTypeProvider
        );
        if (isset($recipeConfig['format'])) {
            $this->recipeDataBag->setFormat($recipeConfig['format']);
        }
        if (isset($recipeConfig['styles']) && 0 < count($recipeConfig['styles'])) {
            $this->stylesRepository->mergeStyles($recipeConfig['styles']);
        }
        $this->recipeDataBag->setStylesRepository($this->stylesRepository);
        $this->processInstructions($recipeConfig['instructions']);

        $writerObject = IOFactory::createWriter($this->recipeDataBag->getPhpWordObject(), 'Word2007');
        if (null === $writerObject) {
            throw DocumentGenerationException::writerObjectGenerationFailed();
        }
        return $writerObject;
    }

    /**
     * @param array $instructions
     * @throws DocumentGenerationException|AccessException
     */
    private function processInstructions(array $instructions): void
    {
        foreach ($instructions as $instruction) {
            $instructionClass = $this->instructionFactory->lookupForName($instruction['name']);

            // How to handle options? Do they have to be declared like in phpWord?
            // And then can just be handed over.
            // Or do we need a mapper to map from options to correct phpWord styles?

            // handle data lookup
            if (array_key_exists('path', $instruction)) {
                [$datapool, $pathArray] = $this->datapoolManager->parsePath($instruction['path']);
                $this->setCurrentInstructionDataFromPath($datapool, $pathArray);
            }

            $instructionClass->initializeInstruction($instruction, $this->recipeDataBag);
            $instructionClass->render();

            // Iterate over children instructions
            if (array_key_exists('children', $instruction)) {
                $this->processInstructions($instruction['children']);
            }

            // Now that all children have been processed, we can remove the structural instruction from the working path
            if ($instructionClass instanceof StructuralInstructionInterface) {
                $this->recipeDataBag->removeFromWorkingPath();
            }

            // recall yourself if iterate is true and there are still entities left in the used datapool
            if (isset($datapool)) {
                $isDatapoolEmpty = $datapool->isEmpty();
                if (array_key_exists('iterate', $instruction) && $instruction['iterate'] && !$isDatapoolEmpty) {
                    $datapool->setNextCurrentEntity();
                    $this->processInstructions([$instruction]);
                }
            }
        }
    }

    /**
     * @throws AccessException
     */
    private function setCurrentInstructionDataFromPath(Datapool $datapool, array $pathArray): void
    {
        if (0 !== count($pathArray)) {
            $currentInstructionData = $datapool->getDataFromPath($pathArray);
            $this->recipeDataBag->setCurrentInstructionData($currentInstructionData);
        }
    }
}
