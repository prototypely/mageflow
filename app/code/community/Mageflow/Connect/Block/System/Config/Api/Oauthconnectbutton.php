<?php

/**
 * Oauthconnectbutton.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Block_System_Config_Api_Oauthconnectbutton
 *
 * Creates "Connect with oauth" button
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_System_Config_Api_Oauthconnectbutton
	extends Mageflow_Connect_Block_System_Config_Api_Basebutton {
	/**
	 * Creates "test api" button
	 *
	 * @param Mage_Core_Block_Abstract $buttonBlock
	 *
	 * @return string
	 */
	public function getButtonData( $buttonBlock ) {
		$url  = Mage::helper( "adminhtml" )->getUrl( 'adminhtml/oauth_initiate/index' ) . '?isAjax=false';
		$data = array(
			'label'       => Mage::helper( 'mageflow_connect' )->__(
				"Connect to target Magento"
			),
			'class'       => 'add',
			'comment'     => 'Connects this Magento instance to another Magento instance with Oauth',
			'id'          => "btn_oauthconnect",
			'after_html'  => $this->getAfterHtml(),
			'before_html' => $this->getBeforeHtml(),
			'onclick'     => "setLocation('" . $url . "')",

		);

		return $data;
	}

	/**
	 * Returns HTML that is prepended to button
	 *
	 * @return string
	 */
	protected function getBeforeHtml() {
		$html = '';

		return $html;
	}

	/**
	 * Returns HTML that is appended to button
	 *
	 * @return string
	 */
	protected function getAfterHtml() {
		$html = '';

		return $html;
	}

}