<?php

/**
 * Code.php
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
 * Mageflow_Connect_Model_Mapper_Storegroup_Code
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Mapper_Storegroup_Code extends Mageflow_Connect_Model_Mapper_Base
{

    /**
     * Search for store names by ID-s and replace ID-s with names for better interoperability
     *
     * @param $fromValue
     * @param Mage_Core_Model_Store_Group $context
     * @return mixed|void
     */
    public function mapValue($fromValue, $context)
    {
        $toValue = array();
        if (is_scalar($fromValue)) {
            /**
             * @var Mage_Core_Model_Store_Group $storeGroup
             */
            $storeGroup = Mage::getModel('core/store_group')->load($fromValue);
            $toValue[] = $storeGroup->getName();
        } elseif (is_array($fromValue)) {
            foreach ($fromValue as $storeId) {
                /**
                 * @var Mage_Core_Model_Store_Group $store
                 */
                $storeGroup = Mage::getModel('core/store_group')->load($storeId);
                $toValue[] = $storeGroup->getName();
            }
        }
        $this->log($toValue);
        return parent::mapValue($toValue, $context);
    }
} 