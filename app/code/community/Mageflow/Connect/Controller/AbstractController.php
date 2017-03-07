<?php

/**
 * AbstractController.php
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

//needed for custom classcloading and namespaces
require_once 'Mageflow/Connect/Module.php';

/**
 * Mageflow_Connect_Controller_AbstractController
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Controller
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Controller_AbstractController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Class constuctor
     */
    public function _construct()
    {
        //include Mageflow client lib and its autoloader
        $m = new \Mageflow\Connect\Module();
    }

    /**
     * @param $msg
     */
    protected function log($msg)
    {
        Mage::helper('mageflow_connect/log')->log($msg);
    }

    /**
     * Returns MageFlow API client instance with
     * authentication fields filled in
     *
     * @return \Mageflow\Connect\Model\Api\Mageflow\Client
     */
    public function getApiClient()
    {
        $client = Mage::helper('mageflow_connect/oauth')->getApiClient();

        return $client;
    }

    /**
     * @return Mageflow_Connect_Helper_Type
     */
    protected function getTypeHelper()
    {
        return Mage::helper('mageflow_connect/type');
    }

    /**
     * @param string $typeName
     * @return Mageflow_Connect_Model_Interfaces_Dataprocessor
     */
    protected function getDataProcessor($typeName)
    {
        $processorClass = $this->getTypeHelper()->getHandlerClass($typeName);

        if ($processorClass != '') {
            $processor = Mage::getModel($processorClass);
        } else {
            $processor = Mage::getModel('mageflow_connect/handler_generic');
        }
        return $processor;
    }

    /**
     * Checks if user is still logged in
     */
    protected function _expireAjax()
    {
        /**
         * @var Mage_Core_Model_Session $session
         */
        Mage::getSingleton('core/session', array('name' => 'adminhtml'));
        if (!Mage::getSingleton('admin/session')->isLoggedIn()) {
            return $this->_ajaxRedirect();
        }
        return null;
    }

    /**
     * Redirects user to login page
     * @param null $url
     * @return $this
     */
    protected function _ajaxRedirect($url = null)
    {
        if (null === $url) {
            $this->getResponse()
                ->setHeader('HTTP/1.1', '403 Session Expired', true)
                ->setHeader('Login-Required', 'true', true)
                ->sendResponse();
        }

        return $this;
    }
}
