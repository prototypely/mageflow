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
 * @license    MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Model_Api2_Ping_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license    MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Ping_Rest_Admin_V1
	extends Mageflow_Connect_Model_Api2_Abstract {


	/**
	 * retrieve
	 *
	 * @return array
	 */
	public function _retrieve() {
		$out              = array();
		$out['timestamp'] = time();
		/**
		 * @var Mageflow_Connect_Model_System_Info_Cpu $cpuInfoModel
		 */
		$cpuInfoModel = Mage::getModel( 'mageflow_connect/system_info_cpu' );
		$load         = $cpuInfoModel->getSystemLoad();

		$coreCount = $cpuInfoModel->getCpuCores();

		$coreCount          = ( $coreCount > 0 ) ? $coreCount : 1;
		$balancedLoad       = $load / $coreCount;
		$out['system_load'] = round(
			$balancedLoad,
			2
		);
		$freeDisk           = disk_free_space( dirname( __FILE__ ) );
		$totalDisk          = disk_total_space( dirname( __FILE__ ) );
		$out['free_disk']   = round( ( $freeDisk / $totalDisk ) * 100, 2 );

		/**
		 * @var Mageflow_Connect_Model_System_Info_Session $sessionInfoModel
		 */
		$sessionInfoModel       = Mage::getModel( 'mageflow_connect/system_info_session' );
		$out['active_sessions'] = $sessionInfoModel->getNumberOfActiveSessions();

		if ( Mage::app()->getStore()->getConfig( Mageflow_Connect_Model_System_Config::API_INSTANCE_KEY ) == "" ) {
			$instanceKey = substr(
				sha1(
					Mage::app()->getStore()->getConfig( Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL )
				),
				0,
				6 );
			Mage::app()->getConfig()->saveConfig(
				Mageflow_Connect_Model_System_Config::API_INSTANCE_KEY,
				$instanceKey
			);
		}

		$out['instance_key'] = Mage::app()->getStore()
		                           ->getConfig( Mageflow_Connect_Model_System_Config::API_INSTANCE_KEY );
		$out['base_url']     = Mage::app()->getStore()->getConfig( Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL );

		$out['mfx_version'] = Mage::app()->getConfig()
		                          ->getNode( 'modules/Mageflow_Connect/version' )
		                          ->asArray();

		return $out;
	}

}
