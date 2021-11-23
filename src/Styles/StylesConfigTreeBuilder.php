<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Styles;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class StylesConfigTreeBuilder implements ConfigurationInterface
{

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('styles');
        $treeBuilder->getRootNode()
            ->arrayPrototype()
                ->performNoDeepMerging()
                ->info('There can be multiple styles definitions. These are the global definitions accessible by all recipes. Can be overwritten in recipes and on every instruction.')
                ->children()
                    ->arrayNode('attributes')
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end();
        return $treeBuilder;
    }
}
