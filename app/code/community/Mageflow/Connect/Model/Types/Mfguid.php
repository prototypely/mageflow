<?php

/**
 * Mfguid.php
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
 * Mageflow_Connect_Model_Types_Mfguid
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Types_Mfguid extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Set MFGUID
     *
     * @param Mage_Core_Model_Object $object
     * @return Mageflow_Connect_Model_Types_Mfguid
     */
    public function beforeSave($object)
    {
        /**
         * @var Mageflow_Connect_Helper_Data $helper
         */
        $helper = Mage::helper('mageflow_connect');
        $attributeCode = $this->getAttribute()->getAttributeCode();
        if ($object->isObjectNew() && is_null($object->getData($attributeCode))) {
            $object->setData($attributeCode, $helper->randomHash(32));
        }

        return $this;
    }
} 