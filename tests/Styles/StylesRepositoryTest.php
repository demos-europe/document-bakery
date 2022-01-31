<?php

namespace DemosEurope\DocumentBakery\Tests\Styles;

use DemosEurope\DocumentBakery\Exceptions\StyleException;
use DemosEurope\DocumentBakery\Styles\ConfigStylesLoader;
use DemosEurope\DocumentBakery\Styles\StylesRepository;
use DemosEurope\DocumentBakery\Tests\BakeryFunctionalTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class StylesRepositoryTest extends BakeryFunctionalTestCase
{
    /**
     * @var StylesRepository
     */
    protected $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $recipeConfigLoader = new ConfigStylesLoader($this->config['styles']);
        $configIterable = new ArrayCollection([$recipeConfigLoader]);
        $this->sut = new StylesRepository($configIterable);
    }

    public function testGetException(): void
    {
        $this->expectException(StyleException::class);
        $this->sut->get('wrongRecipe');
    }

    public function testGetSuccessful(): void
    {
        $result = $this->sut->get('GlobalTable');
        self::assertEquals($this->config['styles']['GlobalTable'], $result);
    }

    public function testHas(): void
    {
        // Unsuccessful
        $result1 = $this->sut->has('wrongStyle');
        self::assertNotTrue($result1);
        // Successful
        $result2 = $this->sut->has('GlobalTable');
        self::assertTrue($result2);
    }

    public function testMergeStyles(): void
    {
        $result1 = $this->sut->has('newStyle');
        self::assertNotTrue($result1);
        $this->sut->mergeStyles([
            'newStyle' => [
                'attributes' => [
                    'borderSize' => 6
                ]
            ]
        ]);
        $result2 = $this->sut->has('newStyle');
        self::assertTrue($result2);
        $newStyle1 = $this->sut->get('newStyle');
        self::assertEquals(6, $newStyle1['attributes']['borderSize']);
        $this->sut->mergeStyles([
            'newStyle' => [
                'attributes' => [
                    'borderSize' => 7
                ]
            ]
        ]);
        $newStyle2 = $this->sut->get('newStyle');
        self::assertEquals(7, $newStyle2['attributes']['borderSize']);
    }
}
