<?php

require_once Mage::getModuleDir('controllers', 'Mage_Oauth') . DS . 'Adminhtml' . DS . 'Oauth' . DS
    . 'AuthorizeController.php';

/**
 * CallbackController.php
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
 * Mageflow_Connect_Oauth_CallbackController
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Controller
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Oauth_CallbackController extends Mage_Oauth_Adminhtml_Oauth_AuthorizeController
{
    const REQUEST_TIMEOUT = 300;

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

    public function indexAction()
    {
        if ($_SESSION['MAGENTO_REQUEST_TOKEN'] || !$_SESSION['MAGENTO_ACCESS_TOKEN']) {

            $config = Mage::helper('mageflow_connect/oauth')->getConfig();

            $consumer = new Zend_Oauth_Consumer($config);

            $requestToken = unserialize($_SESSION['MAGENTO_REQUEST_TOKEN']);

            $accessToken = $consumer->getAccessToken($_GET, $requestToken);

            Mage::app()->getConfig()->saveConfig(
                Mageflow_Connect_Model_System_Config::API_TOKEN,
                Mage::helper('core')->encrypt($accessToken->getToken())
            );
            Mage::app()->getConfig()->saveConfig(
                Mageflow_Connect_Model_System_Config::API_TOKEN_SECRET,
                Mage::helper('core')->encrypt($accessToken->getTokenSecret())
            );

            $instanceKey = Mage::app()->getStore()->getConfig(Mageflow_Connect_Model_System_Config::API_INSTANCE_KEY);
            if (empty($instanceKey)) {
                $instanceKey = substr(
                    sha1(
                        Mage::app()->getStore()->getConfig(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL)
                    ),
                    0,
                    6);
                Mage::app()->getConfig()->saveConfig(
                    Mageflow_Connect_Model_System_Config::API_INSTANCE_KEY,
                    $instanceKey
                );
            }

            $_SESSION['MAGENTO_ACCESS_TOKEN'] = serialize($accessToken);
            $_SESSION['MAGENTO_REQUEST_TOKEN'] = null;

        }
        $url = Mage::helper('adminhtml')->getUrl('adminhtml/oauth_callback/test');

        return $this->_redirectUrl($url);
    }

    /**
     * Tests Oauth connection
     *
     * @throws Zend_Http_Client_Adapter_Exception
     * @throws Zend_Http_Client_Exception
     */
    public function testAction()
    {
        /**
         * @var Mageflow_Connect_Helper_Oauth $helper
         */
        $helper = Mage::helper('mageflow_connect/oauth');
        $client = $helper->getApiClient();
        $response = $client->get('ping');
        $status = $response->getStatus();
        $url = Mage::helper('adminhtml')
            ->getUrl('adminhtml/system_config/edit/section/mageflow_connect',
                array('code' => $status));
        $_SESSION['MAGENTO_ACCESS_TOKEN'] = null;

        return $this->_redirectUrl($url);

    }

}