<?php

/**
 * Connectmf.php
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
 * Mageflow_Connect_Block_System_Config_Api_Connectmf
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
class Mageflow_Connect_Block_System_Config_Api_Connectmf
    extends Mageflow_Connect_Block_System_Config_Api_Basebutton
{
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
                    "Connect to MageFlow"
                ),
            'class' => '',
            'comment' => '',
            'id' => 'btn_connect_mf',
            'data-api-url' => Mage::helper("adminhtml")->getUrl(
                    'adminhtml/ajax/gettoken'
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
        $link = $this->getSignupUrl();
        $html
            = <<<HTML
            <p>
            Please <a href="$link" target="_blank">click here to sign up</a> if you don't have an account at MageFlow yet.
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

    /**
     * get signup url
     */
    protected function getSignupUrl()
    {
        return Mage::app()->getStore()->getConfig(Mageflow_Connect_Model_System_Config::SIGNUP_URL);
    }

}
