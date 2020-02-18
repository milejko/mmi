<?php

namespace Mmi\App;

use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class PneFrontControllerTwigPathPlugin extends FrontControllerPluginAbstract
{
    private function extractPath(string $path): string
    {
        return preg_replace('/^(.*\/template)(.*)/i', '$1', realpath($path));
    }

    private function extract($data, array &$paths){
        if(is_array($data)){
            foreach($data as $val){
                $this->extract($val, $paths);
            }
        }
        if(is_string($data)){
            $paths[] = $this->extractPath($data);
        }
    }

    public function preDispatch(\Mmi\Http\Request $request)
    {
        $loader = \App\Registry::$twig->getLoader();
        $this->registerTemplates($loader);
        $this->registerHelpers();

        \App\Registry::$twig->addExtension(new DebugExtension());
    }

    private function registerHelpers(){
        $twig = \App\Registry::$twig;

        //wyszukiwanie helpera w strukturze
        foreach (\Mmi\App\FrontController::getInstance()->getStructure('helper') as $namespace => $helpers) {
            //helper znaleziony
            foreach($helpers as $key => $name){
                $className = '\\' . $namespace . '\\Mvc\\ViewHelper\\' . ucfirst($key);
                $helper = new $className;
                $twig->addFunction(new TwigFunction($key, [$helper, $key]));
                FrontController::getInstance()->getView()->registerHelper($helper);
            }
        }
    }

    /**
     * @param FilesystemLoader $loader
     *
     * @throws \Mmi\App\KernelException
     * @throws \Twig\Error\LoaderError
     */
    public function registerTemplates(FilesystemLoader $loader)
    {
        /** @var $loader FilesystemLoader */
        $templates = array_filter(
            FrontController::getInstance()->getStructure('template'),
            function ($data) {
                return count($data) > 0;
            }
        );
        foreach ($templates as $moduleKey => $templateData) {
            $overridePath = realpath(BASE_PATH . '/templates/' . $moduleKey);
            if (is_dir($overridePath)) {
                $loader->addPath($overridePath, $moduleKey);
            }

            $paths = [];
            foreach ($templateData as $templateDataKey => $templateDataValue) {
                $this->extract($templateDataValue, $paths);
            }
            foreach (array_unique($paths) as $path) {
                $loader->addPath($path, $moduleKey);
            }
        }
}
}
