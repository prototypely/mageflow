<?php

/**
 * Data.php
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
 * Mageflow_Connect_Model_Resource_Config_Data
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Resource_Config_Data
    extends Mage_Core_Model_Resource_Config_Data
{

    /**
     * Overwrites Magento's original config_data resource
     * in order to set created_at and updated_at timestamps
     *
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Mage_Core_Model_Resource_Config_Data
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $dateTime = new Zend_Date();
        $now = $dateTime->toString('c');
        if (!$object->getId()) {
            $object->setCreatedAt($now);
        }

        $object->setUpdatedAt($now);

        return parent::_beforeSave($object);
    }

}
