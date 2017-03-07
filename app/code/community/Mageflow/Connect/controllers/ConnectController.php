<?php

/**
 * ConnectController.php
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
 * Mageflow_Connect_ConnectController
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Controller
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_ConnectController
	extends Mageflow_Connect_Controller_AbstractController {

	/**
	 * Sends Instance connection request to MageFlow and
	 * handles data from response
	 */
	public function connectAction() {
		$instanceKey = $this->getRequest()->getParam( 'instanceKey', null );
		$email       = $this->getRequest()->getParam( 'email', null );
		if ( null !== $instanceKey && null !== $email ) {
			try {

				$onetimeToken = Mage::getModel( 'admin/session' )->getMfToken();

				/**
				 * @var Mageflow_Connect_Helper_Oauth $oauthHelper
				 */
				$oauthHelper = Mage::helper( 'mageflow_connect/oauth' );

				/**
				 * @var Mage_Oauth_Model_Consumer $oauthConsumerModel
				 */
				$oauthConsumerModel = $oauthHelper->createConsumerModel( $instanceKey );

				/**
				 * @var Mage_Oauth_Model_Token $token
				 */
				$token = $oauthHelper->createToken( $oauthConsumerModel );

				$client = $this->getApiClient();

				$data = array(
					'instanceKey'    => $instanceKey,
					'baseUrl'        => Mage::app()->getStore()
					                        ->getConfig( Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL ),
					'secureBaseUrl'  => Mage::app()->getStore()
					                        ->getConfig( Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL ),
					'consumerKey'    => $oauthConsumerModel->getKey(),
					'consumerSecret' => $oauthConsumerModel->getSecret(),
					'token'          => $token->getToken(),
					'tokenSecret'    => $token->getSecret(),
					'onetimeToken'   => $onetimeToken,
					'email'          => $email
				);

				$type = Mage::app()->getStore()->getConfig( Mageflow_Connect_Model_System_Config::API_INSTANCE_TYPE );
				if ( ! is_null( $type ) ) {
					$data['type'] = $type;
				}

				$response = $client->post( 'instancehandshake', $data );

				$responseObject = json_decode( $response );

				Mage::app()->getStore();

				$o = $responseObject->items[0];
				if ( is_object( $o ) ) {
					if ( isset( $o->instanceKey ) ) {
						Mage::app()->getConfig()->saveConfig(
							Mageflow_Connect_Model_System_Config::API_INSTANCE_KEY,
							$o->instanceKey
						);
					}
					if ( isset( $o->consumerKey ) ) {
						Mage::app()->getConfig()->saveConfig(
							Mageflow_Connect_Model_System_Config::API_CONSUMER_KEY,
							$o->consumerKey
						);
					}
					if ( isset( $o->consumerSecret ) ) {
						Mage::app()->getConfig()->saveConfig(
							Mageflow_Connect_Model_System_Config::API_CONSUMER_SECRET,
							$o->consumerSecret
						);
					}
					if ( isset( $o->token ) ) {
						Mage::app()->getConfig()->saveConfig(
							Mageflow_Connect_Model_System_Config::API_TOKEN,
							$o->token
						);
					}
					if ( isset( $o->tokenSecret ) ) {
						Mage::app()->getConfig()->saveConfig(
							Mageflow_Connect_Model_System_Config::API_TOKEN_SECRET,
							$o->tokenSecret
						);
					}
					if ( isset( $o->projectId ) ) {
						Mage::app()->getConfig()->saveConfig(
							Mageflow_Connect_Model_System_Config::API_PROJECT,
							$o->projectId
						);
					}
					if ( isset( $o->projectName ) ) {
						Mage::app()->getConfig()->saveConfig(
							Mageflow_Connect_Model_System_Config::API_PROJECT_NAME,
							$o->projectName
						);
					}
					Mage::app()->getConfig()->saveConfig(
						Mageflow_Connect_Model_System_Config::API_ENABLED,
						1
					);

					$companyId = $o->companyId;
					if ( $companyId > 0 ) {
						$arr = array( 'id' => $companyId, 'name' => $o->companyName );
						Mage::app()->getConfig()->saveConfig(
							Mageflow_Connect_Model_System_Config::API_COMPANY,
							$companyId
						);
						Mage::app()->getConfig()->saveConfig(
							Mageflow_Connect_Model_System_Config::API_COMPANY_NAME,
							serialize( $arr )
						);
					}

					$message  = sprintf( 'Connected this instance (%s) successfully to MageFlow API in project (%s)',
						$o->instanceKey, $o->projectName );
					$severity = Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE;

					$this->log( 'Finished handshake' );

					// connecting was a success, let's refresh pull grid cache too

					/**
					 * @var Mageflow_Connect_Model_Async_Itemcacheupdater $pullGridUpdaterModel
					 */
					$pullGridUpdaterModel = Mage::getModel( 'mageflow_connect/async_itemcacheupdater' );
					$pullGridUpdaterModel->run();

					/**
					 * Create admin user with user's mageflow e-mail in order to make automatic admin login work
					 */

					/**
					 * @var Mageflow_Connect_Helper_Oauth $oauthHelper
					 */
					$oauthHelper = Mage::helper( 'mageflow_connect/oauth' );
					$adminUser   = $oauthHelper->createAdminUser( $email, 'Admin', 'User',
						sha1( uniqid( uniqid( 'hifive' ) ) ), false );

					if ( $adminUser->getId() < 1 ) {
						$this->log( 'Creation of admin user failed' );
					}

				} else {
					$this->log( 'Handshake FAILED' );
					$message
						      = sprintf( 'Connecting instance (%s) to MageFlow failed. Please try again later or contact MageFlow Customer Support.',
						$o->instanceKey );
					$severity = Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR;
				}
			} catch ( Exception $ex ) {
				$this->log( 'Handshake of instance failed. Details follow' );
				$this->log( $ex->getMessage() );
				$this->log( $ex->getTraceAsString() );
				$message
					      = 'Connecting instance to MageFlow failed. Please try again later or contact MageFlow Customer Support.';
				$severity = Mage_AdminNotification_Model_Inbox::SEVERITY_CRITICAL;
			}
			/**
			 * @var Mageflow_Connect_Helper_Notification $notificationHelper
			 */
			$notificationHelper = Mage::helper( 'mageflow_connect/notification' );
			$notificationHelper->postNotification( $message, $severity );
		}

		return $this->_redirect( 'adminhtml/system_config/edit/section/mageflow_connect' );

	}
}
