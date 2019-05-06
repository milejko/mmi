<?php

namespace Mmi\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class CmsConfiguration
 * @package Mmi\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('cms');
        $node        = $treeBuilder->getRootNode();
        
        $node
            ->children()
                ->arrayNode('database')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->children()
                        ->scalarNode('host')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('port')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('username')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('password')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('database')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end();
        
        return $treeBuilder;
    }
}
