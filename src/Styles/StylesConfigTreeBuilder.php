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
                ->info('There can and will be multiple recipes. All need to follow this schema.')
                ->children()
                    ->scalarNode('instruction_type')
                    ->end()
                    ->arrayNode('attributes')
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end();
        return $treeBuilder;
    }
}
