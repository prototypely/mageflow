<?php

/**
 * V1.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Model_Api2_Help_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Help_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{

    /**
     * retrieve
     *
     * @return array
     */
    public function _retrieve()
    {
        $config = $this->getConfig();
        $out = array();
        $out['info']
            = 'NB! Please mind the original Magento resource
            names as these may not be correct in this listing';
        foreach ($config->getResources() as $name => $resource) {
            $attributes = array();
            foreach (
                $config->getResourceAttributes($name) as $attributeNode =>
                $attributeText
            ) {
                $attributes[] = $attributeNode;
            }
            $routesArr = array();
            $routes = $this->getConfig()->getNode(
                'resources/' . $name . '/routes'
            );
            if ($routes instanceof Mage_Core_Model_Config_Element) {
                foreach ($routes->children() as $routeName => $route) {
                    $item = array(
                        'name'  => $routeName,
                        'route' => (string)$route->route
                    );
                    $routesArr[] = $item;
                }
                $resourceArr = array(
                    'resource_type' => $name,
                    'routes'        => $routesArr,
                    'attributes'    => $attributes
                );
                $out['resources'][] = $resourceArr;
            }
        }
        return $out;
    }

}
