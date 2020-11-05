<?php

use Mmi\App\AppProfilerInterface;
use Mmi\Cache\PrivateCache;
use Mmi\Mvc\Structure;
use Mmi\Translate;
use Psr\Container\ContainerInterface;

return [
    Translate::class => function (ContainerInterface $container) {
        //loading buffered translator
        $cache = $container->get(PrivateCache::class);
        //loading from cache
        if ($cache->isActive() && (null !== ($translate = $cache->load($cacheKey = 'mmi-translate')))) {
            //wczytanie obiektu translacji z bufora
            $container->get(AppProfilerInterface::class)->event('Mmi\Translate: load translate cache');
            return $translate;
        }
        //utworzenie obiektu tłumaczenia
        $translate = new Translate;
        //dodawanie tłumaczeń do translatora
        foreach (Structure::getStructure('translate') as $translationFile) {
            $translate->addTranslation($translationFile, substr(basename($translationFile), 0, -4));
        }
        //zapis do cache
        if ($cache->isActive()) {
            $cache->save($translate, $cacheKey, 0);
        }
        //event profilera
        $container->get(AppProfilerInterface::class)->event('Mmi\Translate: translations added');
        return $translate;
    },
];
