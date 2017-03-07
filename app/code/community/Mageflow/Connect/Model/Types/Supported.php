<?php

/**
 * Supported.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Model_Types_Supported
 * This class specifies types that are supported by MageFlow Extension
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Types_Supported extends Varien_Object
{
    /**
     * This method returns list of types that
     * MageFlow supports.
     * NB! This list may change over MFx version changes.
     *
     * @return array
     */
    public static function getSupportedTypes()
    {
        /**
         * @var Mageflow_Connect_Helper_Type $helper
         */
        $helper = Mage::helper('mageflow_connect/type');
        return $helper->getSupportedTypes();
    }
}