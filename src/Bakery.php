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
use Doctrine\ORM\EntityManagerInterface;
use EightDashThree\Querying\ConditionParsers\Drupal\DrupalFilterParser;
use EightDashThree\Wrapping\Contracts\AccessException;
use EightDashThree\Wrapping\TypeProviders\PrefilledTypeProvider;
use PhpOffice\PhpWord\Element\AbstractElement;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Writer\WriterInterface;

class Bakery
{
    /** @var InstructionFactory */
    private $elementFactory;

    /** @var AbstractElement */
    public $currentStructureElement;

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

    public function __construct(
        InstructionFactory     $elementFactory,
        EntityManagerInterface $entityManager,
        DrupalFilterParser     $drupalFilterParser,
        RecipeRepository       $recipeRepository,
        PrefilledTypeProvider  $prefilledTypeProvider
    )
    {
        $this->elementFactory = $elementFactory;
        $this->recipeDataBag = new RecipeDataBag();
        $this->drupalFilterParser = $drupalFilterParser;
        $this->recipeRepository = $recipeRepository;
        $this->entityManager = $entityManager;
        $this->prefilledTypeProvider = $prefilledTypeProvider;
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
        $this->processElements($recipeConfig['elements']);

        $writerObject = IOFactory::createWriter($this->recipeDataBag->getPhpWordObject(), 'Word2007');
        if (null === $writerObject) {
            throw DocumentGenerationException::writerObjectGenerationFailed();
        }
        return $writerObject;
    }

    /**
     * @param array $elements
     * @throws DocumentGenerationException|AccessException
     */
    private function processElements(array $elements): void
    {
        foreach ($elements as $element) {
            $elementClass = $this->elementFactory->lookupForName($element['name']);

            // How to handle options? Do they have to be declared like in phpWord?
            // And then can just be handed over.
            // Or do we need a mapper to map from options to correct phpWord styles?

            // handle data lookup
            if (array_key_exists('path', $element)) {
                [$datapool, $pathArray] = $this->datapoolManager->parsePath($element['path']);
                $this->setCurrentElementDataFromPath($datapool, $pathArray);
            }

            $elementClass->setCurrentConfigElement($element);
            $elementClass->setDataFromRecipeDataBag($this->recipeDataBag);
            $elementClass->render();

            // Iterate over children elements
            if (array_key_exists('children', $element)) {
                $this->processElements($element['children']);
            }

            // Now that all children have been processed, we can remove the structural element from the working path
            if ($elementClass instanceof StructuralInstructionInterface) {
                $this->recipeDataBag->removeFromWorkingPath();
            }

            // recall yourself if iterate is true and there are still entities left in the used datapool
            if (isset($datapool)) {
                $isDatapoolEmpty = $datapool->isEmpty();
                if (array_key_exists('iterate', $element) && $element['iterate'] && !$isDatapoolEmpty) {
                    $datapool->setNextCurrentEntity();
                    $this->processElements([$element]);
                }
            }
        }
    }

    /**
     * @param Datapool $datapool
     * @param array $pathArray
     * @throws AccessException
     */
    private function setCurrentElementDataFromPath(Datapool $datapool, array $pathArray): void
    {
        if (0 !== count($pathArray)) {
            $currentElementData = $datapool->getDataFromPath($pathArray);
            $this->recipeDataBag->setCurrentElementData($currentElementData);
        }
    }
}
