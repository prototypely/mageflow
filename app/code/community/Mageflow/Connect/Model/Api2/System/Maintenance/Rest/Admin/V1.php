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
 * Mageflow_Connect_Model_Api2_System_Maintenance_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_System_Maintenance_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'system_maintenance';

    /**
     * Returns array with system info
     *
     * @return array
     */
    public function _retrieve()
    {
        $out = array();
        $this->log($this->getRequest()->getParams());

        $out['system_maintenance'] = Mage::app()
            ->getStore()
            ->getConfig('mageflow_connect/system/maintenance_mode');

        $out['ip_whitelist'] = array();
        $allowIps = Mage::app()->getStore()->getConfig(
            'dev/restrict/allow_ips'
        );
        if (trim($allowIps) != '') {
            $out['ip_whitelist'] = array_map(
                'trim',
                explode(',', $allowIps)
            );
        }
        $this->log($out);

        return $this->prepareResponse($out);
    }

    /**
     * retrieve collection
     *
     * @return array
     */
    public function _retrieveCollection()
    {
        return $this->_retrieve();
    }

    /**
     * update
     *
     * @param array $filteredData
     *
     * @return array
     */
    public function _update(array $filteredData)
    {
        $this->log($filteredData);
        $maintenanceModeEnabled = (int)$filteredData['system_maintenance'];
        if (isset($filteredData['system_maintenance'])) {

            $this->log('cleaning cache BEFORE');
            $this->cleanCache();

            $this->log(
                'setting maintenance mode to '
                . $filteredData['system_maintenance']
            );
            Mage::app()->getConfig()->saveConfig(
                Mageflow_Connect_Model_System_Config::SYSTEM_MAINTENANCE_MODE,
                $maintenanceModeEnabled
            );
        }
        if (isset($filteredData['ip_whitelist'])
            && is_array($filteredData['ip_whitelist'])
            && sizeof($filteredData['ip_whitelist']) > 0
        ) {
            Mage::app()->getConfig()->saveConfig(
                Mageflow_Connect_Model_System_Config::DEV_RESTRICT_ALLOW_IPS,
                implode(',', $filteredData['ip_whitelist'])
            );
        } else {
            if ($maintenanceModeEnabled) {
                Mage::app()->getConfig()->saveConfig(
                    Mageflow_Connect_Model_System_Config::DEV_RESTRICT_ALLOW_IPS,
                    ''
                );
            }
        }


        $this->log('cleaning cache AFTER');
        $this->cleanCache();

        $this->_successMessage(
            'Set maintenance mode = ' . $filteredData['system_maintenance'],
            200,
            $filteredData
        );

        return $filteredData;
    }


}