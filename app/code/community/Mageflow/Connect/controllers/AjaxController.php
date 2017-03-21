<?php

/**
 * AjaxController.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Controller
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_AjaxController
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Controller
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_AjaxController
	extends Mageflow_Connect_Controller_AbstractController {

	/**
	 * test connection to MF API
	 */
	public function testapiAction() {
		$this->log( 'testing' );
		$client = $this->getApiClient();
		/**
		 * @var Zend_Http_Response $response
		 */
		$response = $client->get( 'ping' );
		$this->getResponse()->setHeader( 'Content-Type', 'application/json', true );
		$out           = Mage::helper( 'core' )->jsonDecode( $response->getBody() );
		$out['status'] = $response->getStatus();

		$outJson       = Mage::helper( 'core' )->jsonEncode( $out );
		$this->getResponse()->setBody( $outJson );
	}

	/**
	 * Generate unique token and save it in session for
	 * later reference
	 */
	public function getTokenAction() {
		$helper = Mage::helper( 'mageflow_connect' );

		$connectUrl = Mage::app()->getStore()->getConfig( Mageflow_Connect_Model_System_Config::CONNECT_URL );

		$this->log( 'Using connect URL: ' . $connectUrl );

		$response = new stdClass();

		$response->token = Mage::helper( 'mageflow_connect' )->randomHash( 32 );

		$response->callbackUrl = Mage::helper( 'adminhtml' )->getUrl(
			'adminhtml/connect/connect'
		);

		$response->redirectUrl = $connectUrl;

		$response->comehomeUrl = Mage::helper( 'adminhtml' )->getUrl( 'system_config' );

		$response->instanceKey = Mage::app()->getStore()
		                             ->getConfig( Mageflow_Connect_Model_System_Config::API_INSTANCE_KEY );

		$response->instanceType = Mageflow_Connect_Model_System_Config::INSTANCETYPE_CE;

		$type = Mage::app()->getStore()->getConfig( Mageflow_Connect_Model_System_Config::API_INSTANCE_TYPE );

		if ( ! is_null( $type ) ) {
			$response->instanceType = $type;
		}

		Mage::getModel( 'admin/session' )->setMfToken( $response->token );

		$jsonData = Mage::helper( 'core' )->jsonEncode( $response );

		$this->log( 'Response JSON: ' . $jsonData );

		$this->getResponse()->setHeader( 'Content-Type', 'application/json', true );

		return $this->getResponse()->setBody( $jsonData );
	}

	public function toggleMonitorAction() {
		$this->log( 'toggle instance monitor status' );
		$client      = $this->getApiClient();
		$instanceKey = Mage::app()->getStore()->getConfig( Mageflow_Connect_Model_System_Config::API_INSTANCE_KEY );
		$json        = $client->get( sprintf( 'instance/%s/toggle_monitor', $instanceKey ) );
		$this->getResponse()->setHeader( 'Content-Type', 'application/json', true );

		return $this->getResponse()->setBody( $json );
	}
}
