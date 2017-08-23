<?php

namespace App;

/**
 * Plugin front kontrolera (hooki)
 */
class TestFrontControllerPlugin extends \Mmi\App\FrontControllerPluginAbstract
{

    public function routeStartup(\Mmi\Http\Request $request)
    {
        $request->testArray = [0, 1, 2];
        $request->routeStarup = 1;
    }

    /**
     * Przed uruchomieniem dispatchera
     * @param \Mmi\Http\Request $request
     */
    public function preDispatch(\Mmi\Http\Request $request)
    {
        $request->preDispatch = 1;
    }

    /**
     * Wykonywana po dispatcherze
     * @param \Mmi\Http\Request $request
     */
    public function postDispatch(\Mmi\Http\Request $request)
    {
        $request->postDispatch = 1;
    }

}
