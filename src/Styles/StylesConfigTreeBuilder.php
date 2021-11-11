<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Styles;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class StylesConfigTreeBuilder implements ConfigurationInterface
{

    public function getConfigTreeBuilder(): TreeBuilder
    {
        return new TreeBuilder('styles');
    }
}
