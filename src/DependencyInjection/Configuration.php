<?php

namespace Fchris82\TimeTravellerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('time_traveller');

        $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('time_passing')
                ->isRequired()
            ->end();

        return $treeBuilder;
    }
}
