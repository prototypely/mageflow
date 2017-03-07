<?php

/**
 * Projectlist.php
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
 * Mageflow_Connect_Model_System_Config_Api_Projectlist
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_System_Config_Api_Projectlist
    extends Mage_Core_Model_Abstract
{
    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (($project = \Mage::app()->getStore()->getConfig(
                \Mageflow_Connect_Model_System_Config::API_PROJECT
            ))
            && ($projectName = \Mage::app()->getStore()->getConfig(
                \Mageflow_Connect_Model_System_Config::API_PROJECT_NAME
            ))
        ) {
            return array($projectName => $projectName);
        }
        return array('' => '');
    }
}
