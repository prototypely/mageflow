<?php

require_once Mage::getModuleDir('controllers', 'Mage_Oauth') . DS . 'Adminhtml' . DS . 'Oauth' . DS
    . 'AuthorizeController.php';

/**
 * RejectController.php
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
 * Mageflow_Connect_Oauth_RejectController
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Controller
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Oauth_RejectController extends Mage_Oauth_Adminhtml_Oauth_AuthorizeController
{

    /**
     * Init titles
     *
     * @return Mage_Oauth_Adminhtml_Oauth_ConsumerController
     */
    public function preDispatch()
    {
        parent::preDispatch();

        return $this;
    }

    /**
     * Handles Oauth authorization rejection
     * @return Mage_Core_Controller_Varien_Action
     */
    public function indexAction()
    {
        Mage::log("Oauth request was rejected");

        $url = Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit/section/mageflow_connect');

        return $this->_redirectUrl($url);
    }

}