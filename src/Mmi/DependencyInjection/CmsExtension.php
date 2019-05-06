<?php

namespace Mmi\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class CmsExtension
 * @package Mmi\DependencyInjection\Cms
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
        $configuration = new CmsConfiguration();
        $loader        = new YamlFileLoader($container, new FileLocator(\dirname(__DIR__) . '/Resources/config'));
        $config        = $this->processConfiguration($configuration, $configs);
        
        $container->setParameter('kernel.secret', $config['secret']);
        $container->setParameter('cms.secret', $config['secret']);
        
        $this->appendDatabaseConfig($config['database'], $container);
        $this->appendLocalizationConfig($config['localization'], $container);
        
        $loader->load('services.yaml');
        
        if (true === $container->getParameter('cms.database.enabled')) {
            $loader->load('services_orm.yaml');
        }
    }
    
    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function appendDatabaseConfig(array $config, ContainerBuilder $container)
    {
        $container->setParameter('cms.database.enabled', $config['enabled']);
        
        if (false === $config['enabled']) {
            return;
        }
        
        $container->setParameter('cms.database.host', $config['host']);
        $container->setParameter('cms.database.username', $config['username']);
        $container->setParameter('cms.database.password', $config['password']);
        $container->setParameter('cms.database.port', $config['port']);
        $container->setParameter('cms.database.name', $config['database_name']);
    }
    
    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function appendLocalizationConfig(array $config, ContainerBuilder $container)
    {
        $container->setParameter('cms.localization.enabled', $config['enabled']);
        
        if (false === $config['enabled']) {
            return;
        }
        
        $container->setParameter('cms.localization.default_language', $config['language']);
        $container->setParameter('cms.database.supported_languages', $config['supported_languages']);
    }
}
