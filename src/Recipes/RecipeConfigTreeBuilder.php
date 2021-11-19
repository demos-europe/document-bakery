<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Recipes;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class RecipeConfigTreeBuilder implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('recipes');

        $treeBuilder->getRootNode()
            ->arrayPrototype()
                ->performNoDeepMerging()
                ->info('There can and will be multiple recipes. All need to follow this schema.')
                ->children()
                    ->arrayNode('queries')
                        ->isRequired()
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode('resource_type')
                                    ->isRequired()
                                ->end()
                                ->variableNode('filter')
                                ->end()
                                ->booleanNode('iterable')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('format')
                        ->children()
                            ->scalarNode('name')
                                ->isRequired()
                            ->end()
                            ->scalarNode('output')
                                ->isRequired()
                            ->end()
                            ->arrayNode('options')
                                ->scalarPrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('styles')
                        ->arrayPrototype()
                            ->performNoDeepMerging()
                            ->info('There can and will be multiple recipes. All need to follow this schema.')
                            ->children()
                                ->scalarNode('instruction_type')
                                ->end()
                                ->arrayNode('attributes')
                                    ->scalarPrototype()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->append($this->addInstructionsArrayNode('instructions'))
                ->end()
            ->end();
        return $treeBuilder;
    }

    public function addInstructionsArrayNode($name, $depth = 0): NodeDefinition
    {
        if (6 > $depth) {
            $treeBuilder =  new TreeBuilder($name, 'variable');
            return $treeBuilder->getRootNode();
        }
        $treeBuilder = new TreeBuilder($name);

        $node = $treeBuilder->getRootNode();
        if ('instructions' === $name) {
            $node->isRequired()
                ->requiresAtLeastOneElement();
        }

        $node->arrayPrototype()
            ->children()
                ->scalarNode('name')
                ->isRequired()
                ->end()
                ->scalarNode('content')
                ->end()
                ->scalarNode('path')
                ->end()
                ->booleanNode('iterate')
                ->end()
                ->variableNode('options')
                ->end()
                ->append($this->addInstructionsArrayNode('children', ++$depth))
            ->end()
        ->end();

        return $node;
    }
}
