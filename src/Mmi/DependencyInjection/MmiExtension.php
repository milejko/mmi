<?php

namespace Mmi\DependencyInjection;

use Mmi\Session\Session;
use Mmi\Session\SessionSpace;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class CmsExtension
 * @package Mmi\DependencyInjection\Cms
 */
class MmiExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new MmiConfiguration();
        $loader        = new YamlFileLoader($container, new FileLocator(\dirname(__DIR__) . '/Resources/config'));
        $config        = $this->processConfiguration($configuration, $configs);
        
        $container->setParameter('kernel.secret', $config['secret']);
        $container->setParameter('mmi.secret', $config['secret']);
        
        $this->appendDatabaseConfig($config['database'], $container);
        $this->appendLocalizationConfig($config['localization'], $container);
        $this->appendSecuritySessionConfig($config['security'], $container);
        
        $loader->load('services.yaml');
        $loader->load('services_security.yaml');
        
        if (true === $container->getParameter('mmi.database.enabled')) {
            $loader->load('services_orm.yaml');
        }
    }
    
    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function appendDatabaseConfig(array $config, ContainerBuilder $container)
    {
        $container->setParameter('mmi.database.enabled', $config['enabled']);
        
        if (false === $config['enabled']) {
            return;
        }
        
        $container->setParameter('mmi.database.host', $config['host']);
        $container->setParameter('mmi.database.username', $config['username']);
        $container->setParameter('mmi.database.password', $config['password']);
        $container->setParameter('mmi.database.port', $config['port']);
        $container->setParameter('mmi.database.name', $config['database_name']);
        
        if (false === $container->hasDefinition($config['database_adapter'])) {
            throw new ServiceNotFoundException($config['database_adapter']);
        }
    
        if (false === $container->hasDefinition($config['database_cache'])) {
            throw new ServiceNotFoundException($config['database_cache']);
        }
        
        $container->setAlias('mmi.orm.adapter.default', $config['database_adapter']);
        $container->setAlias('mmi.orm.cache.default', $config['database_cache']);
    }
    
    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function appendLocalizationConfig(array $config, ContainerBuilder $container)
    {
        $container->setParameter('mmi.localization.enabled', $config['enabled']);
        
        if (false === $config['enabled']) {
            return;
        }
        
        $container->setParameter('mmi.localization.default_language', $config['language']);
        $container->setParameter('mmi.database.supported_languages', $config['supported_languages']);
    }
    
    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function appendSecuritySessionConfig(array $config, ContainerBuilder $container)
    {
        $container->setParameter('mmi.security.name', $config['name']);
        $container->setParameter('mmi.security.cookie_lifetime', $config['cookie_lifetime']);
        $container->setParameter('mmi.security.cookie_path', $config['cookie_path']);
        $container->setParameter('mmi.security.cookie_domain', $config['cookie_domain']);
        $container->setParameter('mmi.security.cookie_secure', $config['cookie_secure']);
        $container->setParameter('mmi.security.cookie_http_only', $config['cookie_http_only']);
        $container->setParameter('mmi.security.cache_expire', $config['cache_expire']);
        $container->setParameter('mmi.security.gc_divisor', $config['gc_divisor']);
        $container->setParameter('mmi.security.gc_max_lifetime', $config['gc_max_lifetime']);
        $container->setParameter('mmi.security.gc_probability', $config['gc_probability']);
        $container->setParameter('mmi.security.handler', $config['handler']);
        $container->setParameter('mmi.security.path', $config['path']);
        $container->setParameter('mmi.security.auth_model', $config['auth_model']);
        $container->setParameter('mmi.security.auth_remember', $config['auth_remember']);
        $container->setParameter('mmi.security.session_space', $config['session_space']);
        
        $sessionSpaceDefinition = new Definition(SessionSpace::class, [
            $container->getParameter('mmi.security.session_space')
        ]);
        $container->setDefinition('mmi.session.session_space', $sessionSpaceDefinition);
        
        $sessionDefinition = new Definition(Session::class, [
            [
                'name'             => $container->getParameter('mmi.security.name'),
                'cookie_lifetime'  => $container->getParameter('mmi.security.cookie_lifetime'),
                'cookie_path'      => $container->getParameter('mmi.security.cookie_path'),
                'cookie_domain'    => $container->getParameter('mmi.security.cookie_domain'),
                'cookie_secure'    => $container->getParameter('mmi.security.cookie_secure'),
                'cookie_http_only' => $container->getParameter('mmi.security.cookie_http_only'),
                'cache_expire'     => $container->getParameter('mmi.security.cache_expire'),
                'gc_divisor'       => $container->getParameter('mmi.security.gc_divisor'),
                'gc_max_lifetime'  => $container->getParameter('mmi.security.gc_max_lifetime'),
                'gc_probability'   => $container->getParameter('mmi.security.gc_probability'),
                'handler'          => $container->getParameter('mmi.security.handler'),
                'path'             => $container->getParameter('mmi.security.path'),
                'auth_model'       => $container->getParameter('mmi.security.auth_model'),
                'auth_remember'    => $container->getParameter('mmi.security.auth_remember'),
            ]
        ]);
        $container->setDefinition('mmi.session.session', $sessionDefinition);
        
    }
}
