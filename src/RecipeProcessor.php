<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery;

use DemosEurope\DocumentBakery\Data\Datapool;
use DemosEurope\DocumentBakery\Data\DatapoolManager;
use DemosEurope\DocumentBakery\Data\RecipeDataBag;
use DemosEurope\DocumentBakery\Exceptions\DocumentGenerationException;
use DemosEurope\DocumentBakery\Exceptions\StyleException;
use DemosEurope\DocumentBakery\Instructions\InstructionFactory;
use DemosEurope\DocumentBakery\Instructions\StructuralInstructionInterface;
use DemosEurope\DocumentBakery\Mapper\PhpWordStyleOptions;
use DemosEurope\DocumentBakery\Styles\StylesRepository;
use EightDashThree\Wrapping\Contracts\AccessException;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Writer\WriterInterface;

class RecipeProcessor
{
    private RecipeDataBag $recipeDataBag;

    private InstructionFactory $instructionFactory;

    private DatapoolManager $datapoolManager;
    private StylesRepository $stylesRepository;

    public function __construct(
        DatapoolManager $datapoolManager,
        InstructionFactory $instructionFactory,
        RecipeDataBag $recipeDataBag,
        StylesRepository $stylesRepository
    )
    {
        $this->recipeDataBag = $recipeDataBag;
        $this->instructionFactory = $instructionFactory;
        $this->datapoolManager = $datapoolManager;
        $this->stylesRepository = $stylesRepository;
    }

    /**
     * @throws DocumentGenerationException|StyleException
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
     * @param array<string, mixed> $instructions
     * @throws DocumentGenerationException|AccessException|StyleException
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

            $mappedStyles = $this->getMappedStyleContent($instruction);
            $instructionClass->initializeInstruction($instruction, $this->recipeDataBag, $mappedStyles);
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
     * @param array<int, string> $pathArray
     * @throws AccessException
     */
    private function setCurrentInstructionDataFromPath(Datapool $datapool, array $pathArray): void
    {
        if (0 !== count($pathArray)) {
            $currentInstructionData = $datapool->getDataFromPath($pathArray);
            $this->recipeDataBag->setCurrentInstructionData($currentInstructionData);
        }
    }

    /**
     * @throws StyleException
     */
    private function getMappedStyleContent(array $instruction): array
    {
        $styleContent = [];
        if (isset($instruction['style']) && 0 < count($instruction['style'])) {
            // get attributes of style
            if (isset($instruction['style']['name'])) {
                $style = $this->stylesRepository->get($instruction['style']['name']);
                $styleContent = $style['attributes'];
            }
            // get local style attributes and merge them into existing styles
            if (isset($instruction['style']['attributes'])) {
                $styleContent = array_replace_recursive($styleContent, $instruction['style']['attributes']);
            }
        }

        // Now we need to map the attributes to the possible phpWord style sets
        return PhpWordStyleOptions::getMappedStyleOptions($styleContent);
    }

}
