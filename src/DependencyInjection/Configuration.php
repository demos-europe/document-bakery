<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\DependencyInjection;


use DemosEurope\DocumentBakery\Recipes\RecipeConfigTreeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder('document_bakery');

        $recipeBuilder = new RecipeConfigTreeBuilder();

        $builder->getRootNode()->append(
            $recipeBuilder->getConfigTreeBuilder()->getRootNode()
        );

        return $builder;
    }
}
