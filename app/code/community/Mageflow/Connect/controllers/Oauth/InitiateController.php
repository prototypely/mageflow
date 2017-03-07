<?php
require_once Mage::getModuleDir( 'controllers', 'Mage_Oauth' )
             . DS . 'Adminhtml' . DS . 'Oauth' . DS . 'AuthorizeController.php';
/**
 * InitiateController.php
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
 * Mageflow_Connect_Oauth_InitiateController
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Controller
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Oauth_InitiateController
	extends Mage_Oauth_Adminhtml_Oauth_AuthorizeController {

	/**
	 * Makes oauth/initiate request to remote part
	 */
	public function indexAction() {
		$config = Mage::helper( 'mageflow_connect/oauth' )->getConfig();
		$consumer         = new Zend_Oauth_Consumer( $config );
		$requestToken = $consumer->getRequestToken( array(), Zend_Http_Client::POST );
		$_SESSION['MAGENTO_REQUEST_TOKEN'] = serialize( $requestToken );
		return $consumer->redirect( array(), $requestToken );
	}


}
