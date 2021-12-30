<?php

namespace DemosEurope\DocumentBakery\Tests\Data;

use DemosEurope\DocumentBakery\Data\RecipeDataBag;
use DemosEurope\DocumentBakery\Data\RecipeDataBagFactory;
use DemosEurope\DocumentBakery\Recipes\RecipeRepository;
use PHPUnit\Framework\TestCase;

class RecipeDataBagFactoryTest extends TestCase
{
    private function createSut(array $dummyReturnArray): RecipeDataBagFactory
    {
        $recipeRepositoryStub = $this->createStub(RecipeRepository::class);
        $recipeRepositoryStub->method('get')->willReturn($dummyReturnArray);

        return new RecipeDataBagFactory($recipeRepositoryStub);
    }

    public function testBuild(): void
    {
        $dummyReturnArray = [
            'format' => [
                'name' => 'A4LandscapeFormat',
                'options' => [
                    'pagination' => 'roman'
                ]
            ],
            'styles' => [
                'GlobalTable' => [
                    'attributes' => [
                        'borderColor' => 'BDE699',
                        'borderSize' => 3,
                        'cellMargin' => 44
                    ]
                ]

            ],
            'queries' => [
                'store' => [
                    'resource_type' => '{{variable1}}',
                    'filter' => [
                        'id_filter' => [
                            'condition' => [
                                'path' => 'id',
                                'value' => '{{variable2}}'
                            ]
                        ]
                    ]
                ]
            ],
            'instructions' => [
                [
                    'name' => 'Text',
                    'content' => 'Some testing content'
                ]
            ]
        ];

        $queryVariables = [
            '{{variable1}}' => 'Some Value',
            '{{variable2}}' => 'Some Other Value',
        ];

        $sut = $this->createSut($dummyReturnArray);

        $testObject = $sut->build('TestRecipe', $queryVariables);

        self::assertInstanceOf(RecipeDataBag::class, $testObject);
        self::assertEquals($dummyReturnArray['format'], $testObject->getFormat());
        self::assertEquals($dummyReturnArray['styles'], $testObject->getStyles());
        self::assertEquals($dummyReturnArray['queries'], $testObject->getQueries());
        self::assertEquals($dummyReturnArray['instructions'], $testObject->getInstructions());
        self::assertEquals($queryVariables, $testObject->getQueryVariables());

    }

}
