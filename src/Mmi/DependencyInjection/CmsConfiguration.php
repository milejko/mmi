<?php

namespace Mmi\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

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
                        ->booleanNode('enabled')
                            ->treatNullLike(false)
                            ->defaultValue(false)
                        ->end()
                        ->scalarNode('host')->end()
                        ->scalarNode('port')->end()
                        ->scalarNode('username')->end()
                        ->scalarNode('password')->end()
                        ->scalarNode('database_name')->end()
                    ->end()
                    ->validate()
                        ->always(function ($data){
                if (false === $data['enabled']) {
                    return $data;
                }
                
                $properties = [
                    'host',
                    'port',
                    'username',
                    'password',
                    'database_name',
                ];
                
                foreach ($properties as $property) {
                    if (false === array_key_exists($property, $data) || true === empty($data[$property])) {
                        throw new InvalidConfigurationException(sprintf(
                            'Node "%s" under "%s" must be properly configured',
                            $property,
                            'cms.database'
                        ));
                    }
                }
            })
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
                        ->booleanNode('enabled')
                            ->treatNullLike(false)
                            ->defaultValue(false)
                        ->end()
                        ->scalarNode('language')->end()
                        ->arrayNode('supported_languages')
                            ->requiresAtLeastOneElement()
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                    ->validate()
                        ->always(function ($data){
                if (false === $data['enabled']) {
                    return $data;
                }
                
                $properties = [
                    'language',
                    'supported_languages'
                ];
                
                foreach ($properties as $property) {
                    if (false === array_key_exists($property, $data) || true === empty($data[$property])) {
                        throw new InvalidConfigurationException(sprintf(
                            'Node "%s" under "%s" must be properly configured',
                            $property,
                            'cms.localization'
                        ));
                    }
                }
                
                return $data;
            })
                    ->end()
                ->end()
            ->end();
    }
}
