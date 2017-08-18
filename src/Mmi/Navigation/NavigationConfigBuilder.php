<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Navigation;

class NavigationConfigBuilder
{

    /**
     * Buduje strukturę drzewiastą na podstawie struktury płaskiej
     * @param array $data
     * @return array
     */
    public static function build(array $data = [])
    {
        if (($data['dateStart'] !== null && $data['dateStart'] > date('Y-m-d H:i:s')) || ($data['dateEnd'] !== null && $data['dateEnd'] < date('Y-m-d H:i:s'))) {
            $data['disabled'] = true;
        }
        //budowanie requestu
        $data['request'] = array_merge($data['params'], ['module' => $data['module'], 'controller' => $data['controller'], 'action' => $data['action']]);
        if (!$data['uri']) {
            $data['uri'] = \Mmi\App\FrontController::getInstance()->getView()->url($data['request'], true, $data['https']);
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
