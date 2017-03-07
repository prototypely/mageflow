<?php

/**
 * LoginController.php
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
 * Mageflow_Connect_LoginController
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Controller
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_LoginController
    extends Mageflow_Connect_Controller_AbstractController
{
    /**
     * public actions
     *
     * @var array
     */
    public $_publicActions = array('index', 'mfloginAction');

    /**
     * Class constructor
     */
    public function _construct()
    {
        parent::_construct();
    }

    /**
     * index action
     */
    public function indexAction()
    {

    }

    /**
     * Make request to MF API to verify one-time token
     * and log in admin user if the token and e-mail are valid
     */
    public function mfloginAction()
    {
        $hash = $this->getRequest()->getParam('hash');
        $id = $this->getRequest()->getParam('id');
        $this->log($hash);
        $client = $this->getApiClient();
        $result = json_decode($client->get('whois', array('id' => $id)), true);

        $this->log($result, true);

        if (!isset($result['items']) || !isset($result['items']['auth_hash']) || $hash !== $result['items']['auth_hash']) {
            $this->_redirect('adminhtml/dashboard/index');
            return;
        }

        $email = $result['items']['email'];

        $adminUserCollection = Mage::getModel('admin/user')->getCollection()
            ->addFieldToFilter('email', $email)
            ->addFieldToFilter('is_active', 1);
        $user = $adminUserCollection->getFirstItem();

        $session = Mage::getSingleton('admin/session');
        $this->log($session->getSessionId());
        Mage::dispatchEvent(
            'admin_user_authenticate_before',
            array(
                'username' => $user->getUsername(),
                'user' => $user
            )
        );

        Mage::dispatchEvent(
            'admin_user_authenticate_after',
            array(
                'username' => $user->getUsername(),
                'password' => null,
                'user' => $user,
                'result' => true,
            )
        );
        $session->setUser($user);
//        $session->setIsFirstVisit(true);
        $session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
        Mage::dispatchEvent(
            'admin_session_user_login_success', array('user' => $user)
        );
        session_write_close();
        $this->_redirect('adminhtml/dashboard/index');
        return;
    }
}
