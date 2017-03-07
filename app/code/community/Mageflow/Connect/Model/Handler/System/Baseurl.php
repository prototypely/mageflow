<?php

/**
 * Baseurl.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Model_Handler_System_Baseurl
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_System_Baseurl extends Mageflow_Connect_Model_Handler_Abstract {

	/**
	 * @param Mage_Core_Model_Store|Mage_Adminhtml_Model_System_Config_Backend_Baseurl $model
	 *
	 * @return stdClass
	 */
	public function packData( Mage_Core_Model_Abstract $model ) {
		$c            = null;
		$defaultStore = Mage::app()->getStore();
		if ( $model instanceof Mage_Core_Model_Store ) {
			$c       = new stdClass();
			$c->code = $model->getCode();
			if ( $model->isAdmin() || $defaultStore->getCode() == Mage_Core_Model_Store::DEFAULT_CODE ) {
				$c->scope    = 'default';
				$c->scope_id = $model->getWebsite()->getMfGuid();
			} elseif ( $defaultStore->getCode() == $model->getCode()
			           || $model->getCode() == Mage_Core_Model_Store::DEFAULT_CODE
			) {
				$c->scope    = 'default';
				$c->scope_id = $model->getWebsite()->getMfGuid();
			} else {
				$c->scope    = 'stores';
				$c->scope_id = $model->getMfGuid();
			}
			$c->unsecure_base_url = $model->getConfig( 'web/unsecure/base_url' );
			$c->secure_base_url   = $model->getConfig( 'web/secure/base_url' );

		} elseif ( $model instanceof Mage_Adminhtml_Model_System_Config_Backend_Baseurl ) {
			$data = $model->getData();
			if ( $data['field'] == 'base_url' && $data['group_id'] == 'unsecure' ) {
				$c                    = new stdClass();
				$c->scope             = $data['scope'];
				$c->scope_id          = $this->getWebsiteCodeMap( array( $data['scope_id'] ) );
				$c->unsecure_base_url = $data['groups']['unsecure']['fields']['base_url']['value'];
				$c->secure_base_url   = $data['groups']['secure']['fields']['base_url']['value'];
			}
		}

		return $c;
	}

	/**
	 * Processes incoming base URL changes
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function processData( array $data ) {
		$data = isset( $data[0] ) ? $data[0] : $data;

		$message = null;
		/**
		 * @var Mage_Core_Model_Store $storeModel
		 */
		$storeModel = Mage::getModel( 'core/store' )->load( $data['code'], 'code' );

		$defaultStore = Mage::app()->getStore();
		if ( $storeModel->getCode() == $defaultStore->getCode() ) {
			$scope   = 'default';
			$scopeId = 0;
		} else {
			$scope   = 'stores';
			$scopeId = $storeModel->getId();
		}

		try {
			Mage::getConfig()
			    ->saveConfig( Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL, $data['secure_base_url'], $scope,
				    $scopeId );
			Mage::getConfig()
			    ->saveConfig( Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL, $data['unsecure_base_url'], $scope,
				    $scopeId );
			Mage::getConfig()->reinit();
		} catch ( Exception $ex ) {
			$message = $ex->getMessage();
			$this->log( $ex->getMessage() );
			$this->log( $ex->getTraceAsString() );
		}

		return $this->sendProcessingResponse( $storeModel, $message );
	}

	public function getPreview( Mageflow_Connect_Model_Interfaces_Changeitem $item ) {
		$out = '';

		$object = json_decode( $item->getContent() );
		if ( $object->unsecure_base_url ) {
			$out = $object->unsecure_base_url;
		}

		return $out;
	}
} 