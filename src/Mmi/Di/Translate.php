<?php

use Mmi\App\App;
use Mmi\App\AppProfilerInterface;
use Mmi\Translate;
use Psr\Container\ContainerInterface;

return [
    Translate::class => function (ContainerInterface $container) {
        //get translator structure
        $structure = $container->get('app.structure')['translate'];
        //loading buffered translator
        $cache = $container->get('PrivateCacheService');
        //loading from cache
        if ($cache->isActive() && (null !== ($translate = $cache->load($cacheKey = 'mmi-translate')))) {
            //wczytanie obiektu translacji z bufora
            $container->get(AppProfilerInterface::class)->event('Translate: load translate cache');
            return $translate;
        }
        //utworzenie obiektu tłumaczenia
        $translate = new \Mmi\Translate;
        //dodawanie tłumaczeń do translatora
        foreach ($structure as $languageData) {
            foreach ($languageData as $lang => $translationData) {
                $translate->addTranslation(is_array($translationData) ? $translationData[0] : $translationData, $lang);
            }
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
