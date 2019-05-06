<?php

namespace Mmi\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Class CmsExtension
 * @package Mmi\DependencyInjection
 */
class CmsExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $loader        = new XmlFileLoader($container, new FileLocator(\dirname(__DIR__) . '/Resources/config'));
        $config        = $this->processConfiguration($configuration, $configs);
        
    }
}
