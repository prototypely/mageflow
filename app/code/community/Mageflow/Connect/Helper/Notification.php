<?php

/**
 * Notification.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Helper_Notification
 *
 * Helper class to post notification to admin notification feed
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Helper_Notification extends Mageflow_Connect_Helper_Data
{

    public function postNotification($message, $severity)
    {
        $now = new Zend_Date();
        /* @var Mage_AdminNotification_Model_Inbox $inboxModel */
        $inboxModel = Mage::getModel('adminnotification/inbox');
        $data = array(
            'title'       => $this->__('MageFlow instance connection status'),
            'description' => $message,
            'severity'    => $severity,
            'date_added'  => $now
        );
        $inboxModel->setData($data);
        $inboxModel->save();
        return $inboxModel;
    }
} 