<?php

namespace Mmi\DependencyInjection;

use Mmi\Session\Session;
use Mmi\Session\SessionSpace;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
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
        $this->appendSecuritySessionConfig($config['security'], $container);
        
        $loader->load('services.yaml');
        $loader->load('services_security.yaml');
        
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
    
    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function appendSecuritySessionConfig(array $config, ContainerBuilder $container)
    {
        $container->setParameter('cms.security.name', $config['name']);
        $container->setParameter('cms.security.cookie_lifetime', $config['cookie_lifetime']);
        $container->setParameter('cms.security.cookie_path', $config['cookie_path']);
        $container->setParameter('cms.security.cookie_domain', $config['cookie_domain']);
        $container->setParameter('cms.security.cookie_secure', $config['cookie_secure']);
        $container->setParameter('cms.security.cookie_http_only', $config['cookie_http_only']);
        $container->setParameter('cms.security.cache_expire', $config['cache_expire']);
        $container->setParameter('cms.security.gc_divisor', $config['gc_divisor']);
        $container->setParameter('cms.security.gc_max_lifetime', $config['gc_max_lifetime']);
        $container->setParameter('cms.security.gc_probability', $config['gc_probability']);
        $container->setParameter('cms.security.handler', $config['handler']);
        $container->setParameter('cms.security.path', $config['path']);
        $container->setParameter('cms.security.auth_model', $config['auth_model']);
        $container->setParameter('cms.security.auth_remember', $config['auth_remember']);
        $container->setParameter('cms.security.session_space', $config['session_space']);
        
        $sessionSpaceDefinition = new Definition(SessionSpace::class, [
            $container->getParameter('cms.security.session_space')
        ]);
        $container->setDefinition('mmi.session.session_space', $sessionSpaceDefinition);
        
        $sessionDefinition = new Definition(Session::class, [
            'name'             => $container->getParameter('cms.security.name'),
            'cookie_lifetime'  => $container->getParameter('cms.security.cookie_lifetime'),
            'cookie_path'      => $container->getParameter('cms.security.cookie_path'),
            'cookie_domain'    => $container->getParameter('cms.security.cookie_domain'),
            'cookie_secure'    => $container->getParameter('cms.security.cookie_secure'),
            'cookie_http_only' => $container->getParameter('cms.security.cookie_http_only'),
            'cache_expire'     => $container->getParameter('cms.security.cache_expire'),
            'gc_divisor'       => $container->getParameter('cms.security.gc_divisor'),
            'gc_max_lifetime'  => $container->getParameter('cms.security.gc_max_lifetime'),
            'gc_probability'   => $container->getParameter('cms.security.gc_probability'),
            'handler'          => $container->getParameter('cms.security.handler'),
            'path'             => $container->getParameter('cms.security.path'),
            'auth_model'       => $container->getParameter('cms.security.auth_model'),
            'auth_remember'    => $container->getParameter('cms.security.auth_remember'),
        ]);
        $container->setDefinition('mmi.session.session', $sessionDefinition);
        
    }
}
