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
        $this->addSessionConfig($node);
        
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
    
    private function addSessionConfig(ArrayNodeDefinition $node) {
        $node
            ->children()
                ->arrayNode('security')
                    ->children()
                        ->scalarNode('name')->isRequired()->cannotBeEmpty()->end()
                        ->integerNode('cookie_lifetime')->defaultValue(0)->cannotBeEmpty()->end()
                        ->scalarNode('cookie_path')->defaultValue('/')->cannotBeEmpty()->end()
                        ->scalarNode('cookie_domain')->defaultValue('')->cannotBeEmpty()->end()
                        ->booleanNode('cookie_secure')->defaultFalse()->end()
                        ->booleanNode('cookie_http_only')->defaultTrue()->end()
                        ->integerNode('cache_expire')->defaultValue(14400)->cannotBeEmpty()->end()
                        ->integerNode('gc_divisor')->defaultValue(1000)->end()
                        ->integerNode('gc_max_lifetime')->defaultValue(28800)->cannotBeEmpty()->end()
                        ->integerNode('gc_probability')->defaultValue(1)->cannotBeEmpty()->end()
                        ->scalarNode('handler')->end()
                        ->scalarNode('path')->defaultValue('/tmp')->end()
                        ->scalarNode('auth_model')->cannotBeEmpty()->end()
                        ->integerNode('auth_remember')->defaultValue(31536000)->end()
                        ->scalarNode('session_space')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
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
