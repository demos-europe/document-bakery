<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Tests;


use DemosEurope\DocumentBakery\Bakery;
use DemosEurope\DocumentBakery\Data\RecipeDataBagFactory;
use DemosEurope\DocumentBakery\Exceptions\DocumentGenerationException;
use DemosEurope\DocumentBakery\Recipes\RecipeProcessorFactory;
use DemosEurope\DocumentBakery\Recipes\RecipeRepository;
use DemosEurope\DocumentBakery\Tests\resources\ResourceType\CookbookResourceType;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Writer\Word2007;

class BakeryTest extends BakeryFunctionalTestCase
{
    protected $sut;
    protected $cookbookResourceType;
    protected function setUp(): void
    {
        parent::setUp();

        $this->cookbookResourceType = $this->getContainer()->get(CookbookResourceType::class);
        $recipeProcessorFactory = $this->getContainer()->get(RecipeProcessorFactory::class);

        $recipeConfig = $this->config['recipes']['RecipeWithoutStyles'];
        $mockRecipeRepository = $this->getMockBuilder(RecipeRepository::class)
            ->disableOriginalConstructor()->getMock();
        $mockRecipeRepository->method('get')->willReturn($recipeConfig);

        $recipeDataBagFactory = new RecipeDataBagFactory($mockRecipeRepository);

        $this->sut = new Bakery($recipeDataBagFactory, $recipeProcessorFactory);
    }

    public function testCreateException(): void
    {
        $this->expectException(DocumentGenerationException::class);
        $this->sut->create('test', [
            'Wrong' => $this->cookbookResourceType
        ]);
    }

    public function testCreateSuccess(): void
    {
        /** @var Word2007 $result2 */
        $result2 = $this->sut->create('test', [
            'Cookbook' => $this->cookbookResourceType
        ]);
        self::assertInstanceOf(Word2007::class, $result2);
        $phpWordObject = $result2->getPhpWord();
        $section = $phpWordObject->getSection(0);
        $elements = $section->getElements();
        self::assertCount(4, $elements);
        self::assertInstanceOf(Text::class, $elements[0]);
        self::assertInstanceOf(Text::class, $elements[1]);
        self::assertInstanceOf(Table::class, $elements[2]);
        self::assertInstanceOf(Text::class, $elements[3]);
    }
}
