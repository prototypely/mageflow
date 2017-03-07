#!/usr/bin/env php
<?php

/**
 * last_access_time.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Shell
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

require_once './abstract.php';

/**
 * Mageflow_Connect_Last_Access_Time
 * outputs last access time. It reads it from
 * mageflow_connect/system_info_performance model. It can be either
 * admin or frontend!
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Shell
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Last_Access_Time extends Mage_Shell_Abstract
{

    /**
     * Shell script business method
     */
    public function run()
    {
        echo $this->getLastAccessTime() . "\n";
    }

    /**
     * Public method that returns last access time of this magento instance
     * @return int
     */
    public function getLastAccessTime()
    {
        $helper = Mage::helper('mageflow_connect');
        if ($helper instanceof Mageflow_Connect_Helper_Data) {
            return $helper->getLastAccessTime();
        }
    }
}

$shell = new Mageflow_Connect_Last_Access_Time();
$shell->run();
