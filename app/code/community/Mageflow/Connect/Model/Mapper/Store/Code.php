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
 * Mageflow_Connect_Model_Mapper_Store_Code
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Mapper_Store_Code extends Mageflow_Connect_Model_Mapper_Base
{

    /**
     * Search for store names by ID-s and replace ID-s with names for better interoperability
     *
     * @param $fromValue
     * @param object $context
     * @return mixed|void
     */
    public function mapValue($fromValue, $context)
    {
        $this->log($fromValue);

        $toValue = array();
        if (null === $fromValue) {
            $storeIdArr = $context->getResource()->lookupStoreIds($context->getId());
            $fromValue = $storeIdArr;
        }
        if (is_scalar($fromValue)) {
            /**
             * @var Mage_Core_Model_Store $store
             */
            $store = Mage::getModel('core/store')->load($fromValue);
            $toValue[] = $store->getName();
        } elseif (is_array($fromValue)) {
            foreach ($fromValue as $storeId) {
                /**
                 * @var Mage_Core_Model_Store $store
                 */
                $store = Mage::getModel('core/store')->load($storeId);
                $toValue[] = $store->getName();
            }
        }
        $this->log($toValue);

        return parent::mapValue($toValue, $context);
    }
} 