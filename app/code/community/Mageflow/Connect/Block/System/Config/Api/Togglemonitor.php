<?php

/**
 * Togglemonitor.php
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
 * Mageflow_Connect_Block_System_Config_Api_Togglemonitor
 *
 * Creates "Connect to MageFlow" button
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_System_Config_Api_Togglemonitor
    extends Mageflow_Connect_Block_System_Config_Api_Basebutton
{
    /**
     * Overloads parent's render in order to make display of button
     * depending on instance connection status (availability of keys)
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string|void
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        //TODO
        //verify instance connection status here
        $instanceConnected =
            (
                Mage::app()->getStore()->getConfig(Mageflow_Connect_Model_System_Config::API_CONSUMER_KEY) != ''
                && Mage::app()->getStore()->getConfig(Mageflow_Connect_Model_System_Config::API_CONSUMER_SECRET) != ''
                && Mage::app()->getStore()->getConfig(Mageflow_Connect_Model_System_Config::API_TOKEN) != ''
                && Mage::app()->getStore()->getConfig(Mageflow_Connect_Model_System_Config::API_TOKEN_SECRET) != ''
            );
        if (!$instanceConnected) {
            return '';
        } else {
            return parent::render($element);
        }
    }

    /**
     * Creates "connect to api" button
     *
     * @param Mage_Core_Block_Abstract $buttonBlock
     *
     * @return string
     */
    public function getButtonData($buttonBlock)
    {
        $data = array(
            'label' => Mage::helper('mageflow_connect')->__(
                    "Toggle monitoring"
                ),
            'class' => '',
            'comment' => '',
            'id' => 'btn_toggle_monitor',
            'data-api-url' => Mage::helper("adminhtml")->getUrl(
                    'adminhtml/ajax/togglemonitor'
                ) . '?isAjax=true',
            'onclick' => 'javascript:;',
            'after_html' => $this->getAfterHtml(),
            'before_html' => $this->getBeforeHtml()
        );
        return $data;
    }

    /**
     * Returns HTML that is prepended to button
     *
     * @return string
     */
    protected function getBeforeHtml()
    {
        $html
            = <<<HTML
            <p>
            By clicking this button monitoring status can be toggled. MageFlow will not monitor this instance nor push Change Items here
            if monitoring is off.
            </p>
HTML;

        return $html;
    }

    /**
     * Returns HTML that is appended to button
     *
     * @return string
     */
    protected function getAfterHtml()
    {
        $html = '';
        return $html;
    }

}
