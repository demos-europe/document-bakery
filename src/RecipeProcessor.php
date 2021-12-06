<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery;

use DemosEurope\DocumentBakery\Data\Datapool;
use DemosEurope\DocumentBakery\Data\DatapoolManager;
use DemosEurope\DocumentBakery\Data\RecipeDataBag;
use DemosEurope\DocumentBakery\Exceptions\DocumentGenerationException;
use DemosEurope\DocumentBakery\Instructions\InstructionFactory;
use DemosEurope\DocumentBakery\Instructions\StructuralInstructionInterface;
use EightDashThree\Wrapping\Contracts\AccessException;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Writer\WriterInterface;

class RecipeProcessor
{
    /** @var RecipeDataBag */
    private $recipeDataBag;
    /** @var InstructionFactory  */
    private $instructionFactory;
    private DatapoolManager $datapoolManager;

    public function __construct(
        DatapoolManager $datapoolManager,
        InstructionFactory $instructionFactory,
        RecipeDataBag $recipeDataBag
    )
    {
        $this->recipeDataBag = $recipeDataBag;
        $this->instructionFactory = $instructionFactory;
        $this->datapoolManager = $datapoolManager;
    }

    /**
     * @throws DocumentGenerationException
     */
    public function createFromRecipe (): ?WriterInterface
    {
        $this->processInstructions($this->recipeDataBag->getInstructions());

        try {
            $writerObject = IOFactory::createWriter($this->recipeDataBag->getPhpWordObject(), 'Word2007');
        } catch (\Exception $e) {
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
