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
 * Mageflow_Connect_Model_Api2_System_Info_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_System_Info_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'system_info';


    /**
     * Returns array with system info
     *
     * @return array
     */
    public function _retrieve()
    {
        $out = array();
        $out['resource_type'] = $this->_resourceType;
        $date = new DateTime();
        $out['updated_at'] = $date->format('c');
        /**
         * @var Mageflow_Connect_Model_System_Info_Memory $memoryModel
         */
        $memoryModel = Mage::getModel('mageflow_connect/system_info_memory');
        $out['total_memory'] = $memoryModel->getTotalMemory();
        $out['free_memory'] = $memoryModel->getFreeMemory();
        $out['memory_usage'] = memory_get_usage(true);

        $out['total_disk'] = disk_total_space(dirname(__FILE__));
        $out['free_disk'] = disk_free_space(dirname(__FILE__));

        /**
         * @var Mageflow_Connect_Model_System_Info_Cpu $cpuModel
         */
        $cpuModel = Mage::getModel('mageflow_connect/system_info_cpu');
        $out['cpu_cores'] = $cpuModel->getCpuCores();
        $out['cpu_load'] = $cpuModel->getSystemLoad();


        /**
         * @var Mageflow_Connect_Model_System_Info_Session $sessionInfoModel
         */
        $sessionInfoModel = Mage::getModel('mageflow_connect/system_info_session');
        $out['active_sessions'] = $sessionInfoModel->getNumberOfActiveSessions();

        $out['platform_info'] = php_uname();

        /**
         * @var Mageflow_Connect_Model_System_Info_Os $osInfoModel
         */
        $osInfoModel = Mage::getModel('mageflow_connect/system_info_os');
        $out['os'] = $osInfoModel->getOsType();

        /**
         * @var Mageflow_Connect_Model_System_Info $systemInfoModel
         */
        $systemInfoModel = Mage::getModel('mageflow_connect/system_info');
        $out['magento_performance_history'] = $systemInfoModel->getPerformanceHistory();

        $out['version'] = Mage::getVersion();

        $out['mfx_version'] = Mage::app()->getConfig()->getNode(
            'modules/Mageflow_Connect/version'
        )->asArray();

        /**
         * @var Mageflow_Connect_Helper_System $systemHelper
         */
        $systemHelper = Mage::helper('mageflow_connect/system');

        $out['cache'] = $systemHelper->cacheSettings();

        return array($out);
    }

}
