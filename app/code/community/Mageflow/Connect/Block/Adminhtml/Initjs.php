<?php

/**
 * Initjs.php
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
 * Mageflow_Connect_Block_Adminhtml_Initjs
 * block for MageFlow backend that loads custom JS
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_Adminhtml_Initjs
	extends Mage_Adminhtml_Block_Template {
	/**
	 * Include JS in the head if section is Mageflow
	 */
	protected function _prepareLayout() {
		$section = $this->getAction()->getRequest()->getParam( 'section', false );
		$controllerModule = $this->getAction()->getRequest()->getControllerModule();
		if ( stristr( $section, 'mageflow_connect' ) || stristr( $controllerModule, 'mageflow_connect' ) ) {
			$this->getLayout()
			     ->getBlock( 'head' )
			     ->addCss( 'mageflow/connect/styles.css' );

			$this->getLayout()
			     ->getBlock( 'mageflow_js_container' )
			     ->addJs( 'mageflow/connect/core.js' );
		}
		parent::_prepareLayout();
	}

	/**
	 * to html
	 *
	 * @return string
	 */
	protected function _toHtml() {
		$section = $this->getAction()->getRequest()->getParam( 'section', false );
		if ( $section == 'mageflow_connect' ) {
			return parent::_toHtml();
		} else {
			return '';
		}
	}

	/**
	 * get module version
	 *
	 * @return string
	 */
	public function getModuleVersion() {
		$arr = Mage::getConfig()->getNode()->xpath( '/*//modules/Mageflow_Connect/version' );

		return (string) $arr[0];
	}

}
