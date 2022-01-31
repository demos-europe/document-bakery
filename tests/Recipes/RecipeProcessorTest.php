<?php

namespace DemosEurope\DocumentBakery\Tests\Recipes;

use DemosEurope\DocumentBakery\Data\DataFetcherFactory;
use DemosEurope\DocumentBakery\Data\RecipeDataBag;
use DemosEurope\DocumentBakery\Instructions\InstructionFactory;
use DemosEurope\DocumentBakery\Recipes\RecipeProcessor;
use DemosEurope\DocumentBakery\Styles\StylesRepository;
use DemosEurope\DocumentBakery\Tests\BakeryFunctionalTestCase;
use DemosEurope\DocumentBakery\Tests\resources\ResourceType\CookbookResourceType;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Writer\WriterInterface;

class RecipeProcessorTest extends BakeryFunctionalTestCase
{
    public function testCreateFromRecipe()
    {
        $dataFetcherFactory = $this->getContainer()->get(DataFetcherFactory::class);
        $instructionFactory = $this->getContainer()->get(InstructionFactory::class);
        $cookbookResourceType = $this->getContainer()->get(CookbookResourceType::class);

        $mockedStylesRepository = $this->getMockedStylesRepository();

        $recipeDataBag = new RecipeDataBag();
        $recipeConfig = $this->config['recipes']['RecipeWithoutStyles'];
        $recipeDataBag->setInstructions($recipeConfig['instructions']);
        $recipeDataBag->setQueries($recipeConfig['queries']);
        $recipeDataBag->setQueryVariables([
            'Cookbook' => $cookbookResourceType
        ]);

        $sut = new RecipeProcessor($dataFetcherFactory, $instructionFactory, $recipeDataBag, $mockedStylesRepository);
        $result = $sut->createFromRecipe();
        // Actual Testing
        self::assertInstanceOf(RecipeProcessor::class, $sut);
        self::assertInstanceOf(WriterInterface::class, $result);
        $phpWordObject = $result->getPhpWord();
        $section = $phpWordObject->getSection(0);
        $elements = $section->getElements();
        self::assertCount(4, $elements);
        self::assertInstanceOf(Text::class, $elements[0]);
        self::assertInstanceOf(Text::class, $elements[1]);
        self::assertInstanceOf(Table::class, $elements[2]);
        self::assertInstanceOf(Text::class, $elements[3]);
    }

    private function getMockedStylesRepository(): StylesRepository
    {
        return $this->getMockBuilder(StylesRepository::class)
            ->disableOriginalConstructor()->getMock();
    }
}
