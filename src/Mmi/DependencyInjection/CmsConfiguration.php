<?php

namespace Mmi\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class CmsConfiguration
 * @package Mmi\DependencyInjection\Cms
 */
class CmsConfiguration implements ConfigurationInterface
{
    
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('cms');
        $node        = $treeBuilder->getRootNode();
        
        $this->addDatabase($node);
        $this->addCmsRequirements($node);
        $this->addTranslationConfig($node);
        
        return $treeBuilder;
    }
    
    private function addDatabase(ArrayNodeDefinition $node) {
        $node
            ->children()
                ->arrayNode('database')
                    ->isRequired()
                    ->children()
                        ->booleanNode('enabled')->isRequired()->treatNullLike(true)->end()
                        ->scalarNode('host')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('port')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('username')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('password')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('database_name')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end();
    }
    
    private function addCmsRequirements(ArrayNodeDefinition $node) {
        $node
            ->children()
                ->scalarNode('secret')->isRequired()->cannotBeEmpty()->end()
            ->end();
    }
    
    private function addTranslationConfig(ArrayNodeDefinition $node) {
        $node
            ->children()
                ->arrayNode('localization')
                    ->isRequired()
                    ->children()
                        ->booleanNode('enabled')->isRequired()->treatNullLike(true)->end()
                        ->scalarNode('language')->defaultValue('en')->isRequired()->cannotBeEmpty()->end()
                        ->arrayNode('supported_languages')
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
