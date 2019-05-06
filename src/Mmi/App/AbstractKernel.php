<?php

namespace Mmi\App;

use Mmi\DependencyInjection\MmiExtension;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * Class AbstractKernel
 * @package Mmi\App
 */
abstract class AbstractKernel extends BaseKernel
{
    /**
     * @param RouteCollectionBuilder $routes
     *
     * @return mixed
     */
    abstract protected function configureRoutes(RouteCollectionBuilder $routes);
    
    /**
     * @param ContainerBuilder $containerBuilder
     * @param LoaderInterface  $loader
     *
     * @throws \Exception
     */
    abstract protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader);
    
    /**
     * @param LoaderInterface $loader
     *
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) use ($loader) {
            $container->loadFromExtension('framework', [
                'router' => [
                    'resource' => 'kernel::loadRoutes',
                    'type' => 'service',
                ],
            ]);
            if ($this instanceof EventSubscriberInterface) {
                $container->register('kernel', static::class)
                    ->setSynthetic(true)
                    ->setPublic(true)
                    ->addTag('kernel.event_subscriber');
            }
            $this->configureContainer($container, $loader);
            
            $container->addObjectResource($this);
        });
    }
    
    /**
     * @param LoaderInterface $loader
     *
     * @return RouteCollection
     */
    public function loadRoutes(LoaderInterface $loader)
    {
        $routes = new RouteCollectionBuilder($loader);
        $this->configureRoutes($routes);
        
        return $routes->build();
    }
    
    /**
     * @param ContainerBuilder $container
     */
    protected function prepareContainer(ContainerBuilder $container)
    {
        $container->registerExtension(new MmiExtension());
        parent::prepareContainer($container);
    }
    
    /**
     * @return iterable|BundleInterface[]|void
     */
    public function registerBundles()
    {
        return [new FrameworkBundle()];
    }
}
