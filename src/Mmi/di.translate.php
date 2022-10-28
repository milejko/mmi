<?php

namespace Mmi\Translate;

use Mmi\App\AppProfilerInterface;
use Mmi\Cache\SystemCacheInterface;
use Mmi\Mvc\Structure;
use Mmi\Translate\TranslateInterface;
use Psr\Container\ContainerInterface;

return [
    TranslateInterface::class => function (ContainerInterface $container) {
        //loading buffered translator
        $cache = $container->get(SystemCacheInterface::class);
        //loading from cache
        if (null !== ($translate = $cache->load($cacheKey = 'mmi-translate'))) {
            //wczytanie obiektu translacji z bufora
            $container->get(AppProfilerInterface::class)->event(Translate::class . ': load translate cache');
            return $translate;
        }
        //utworzenie obiektu tłumaczenia
        $translate = new Translate();
        //dodawanie tłumaczeń do translatora
        foreach (Structure::getStructure('translate') as $translationFile) {
            $translate->addTranslationFile($translationFile, substr(basename($translationFile), 0, -4));
        }
        //zapis do cache
        $cache->save($translate, $cacheKey, 0);
        //event profilera
        $container->get(AppProfilerInterface::class)->event(Translate::class . ': translations added');
        return $translate;
    },
];
