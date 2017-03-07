<?php

/**
 * Testbutton.php
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
 * Mageflow_Connect_Block_System_Config_Api_Testbutton
 *
 * Creates "test api" button
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_System_Config_Api_Testbutton
	extends Mageflow_Connect_Block_System_Config_Api_Basebutton {
	/**
	 * Creates "test api" button
	 *
	 * @param Mage_Core_Block_Abstract $buttonBlock
	 *
	 * @return string
	 */
	public function getButtonData( $buttonBlock ) {
		$data = array(
			'label'        => Mage::helper( 'mageflow_connect' )->__(
				"Test API Connection"
			),
			'class'        => '',
			'comment'      => 'Test MageFlow API',
			'id'           => "btn_apitest",
			'after_html'   => $this->getAfterHtml(),
			'before_html'  => $this->getBeforeHtml(),
			'onclick'      => 'javascript:;',
			'data-api-url' => Mage::helper( "adminhtml" )
			                      ->getUrl( 'adminhtml/ajax/testapi' ) . '?isAjax=true'

		);

		return $data;
	}

	/**
	 * Returns HTML that is prepended to button
	 *
	 * @return string
	 */
	protected function getBeforeHtml() {
		$html
			= <<<HTML
        <div style="    margin-top:5px;">
                Test remote API status:
        </div>
HTML;

		return $html;
	}

	/**
	 * Returns HTML that is appended to button
	 *
	 * @return string
	 */
	protected function getAfterHtml() {
		$moduleVersion = Mage::helper( 'mageflow_connect/data' )->getModuleVersion();
		$moduleName    = Mage::helper( 'mageflow_connect/data' )->getModuleName();
		$instanceKey   = Mage::app()->getStore()->getConfig( Mageflow_Connect_Model_System_Config::API_INSTANCE_KEY );
		$baseUrl       = Mage::app()->getStore()->getConfig( Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL );

		$html
			= <<<HTML
<div class="no-display" id="ApiTestResults">
	<h4>Remote system:</h4>
    <ul>
        <li>Connection status: <span id="api_test_status"></span></li>
        <li>Instance key: <span id="api_test_remote_instance_key"></span></li>
        <li>Base URL: <span id="api_test_remote_base_url"></span></li>
        <li>$moduleName version: <span id="api_test_remote_mfx_version"></span></li>
    </ul>
</div>
	<h4>Local system:</h4>
            <ul>
                <li>Instance key: <strong>$instanceKey</strong></li>
                <li>Base URL: <strong>$baseUrl</strong></li>
                <li>$moduleName version: <strong>$moduleVersion</strong></li>
            </ul>

HTML;

		return $html;
	}

}