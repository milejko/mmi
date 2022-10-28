<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Navigation;

use Mmi\App\App;
use Mmi\Mvc\View;

class NavigationConfigBuilder
{
    /**
     * Buduje strukturę drzewiastą na podstawie struktury płaskiej
     * @param array $data
     * @return array
     */
    public static function build(array $data = [])
    {
        if (($data['dateStart'] && $data['dateStart'] > date('Y-m-d H:i:s')) || ($data['dateEnd'] && $data['dateEnd'] < date('Y-m-d H:i:s'))) {
            $data['disabled'] = true;
        }
        //budowanie requestu
        $data['request'] = array_merge($data['params'], ['module' => $data['module'], 'controller' => $data['controller'], 'action' => $data['action']]);
        if (!$data['uri']) {
            $data['uri'] = App::$di->get(View::class)->url($data['request'], true);
        }
        $build = $data;
        $build['children'] = [];

        if (!empty($data['children'])) {
            foreach ($data['children'] as $child) {
                $build['children'][$child->getId()] = $child->build();
            }
        }
        return $build;
    }
}
