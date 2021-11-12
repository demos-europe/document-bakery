<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\DependencyInjection;


use DemosEurope\DocumentBakery\Recipes\RecipeConfigTreeBuilder;
use DemosEurope\DocumentBakery\Styles\StylesConfigTreeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder('document_bakery');

        $recipeBuilder = new RecipeConfigTreeBuilder();
        $stylesBuilder = new StylesConfigTreeBuilder();

        $builder->getRootNode()->append(
            $recipeBuilder->getConfigTreeBuilder()->getRootNode()
        );

        $builder->getRootNode()->append(
            $stylesBuilder->getConfigTreeBuilder()->getRootNode()
        );

        return $builder;
    }
}
