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
    
        return $treeBuilder;
    }
}
