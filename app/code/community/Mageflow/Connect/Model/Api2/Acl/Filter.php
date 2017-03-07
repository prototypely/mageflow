<?php

/**
 * Filter.php
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
 * Mageflow_Connect_Model_Api2_Acl_Filter
 *
 * Rewrites and extends Mage_Api2_Model_Acl_Filter to make
 * filter logic a bit more flexible
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Acl_Filter extends Mage_Api2_Model_Acl_Filter
{

    /**
     * @param array $retrievedData
     * @return array
     */
    public function out($retrievedData)
    {
        //TODO FIX strict notice about mismatching declaration of this method
        return $this->_customFilter($this->getAttributesToInclude(), new ArrayObject($retrievedData));
    }

    /**
     * Return only the data which keys are allowed.
     * This method replaces native _filter() with somewhat more flexible arrayobject usage
     *
     * @param array $allowedAttributes List of attributes available to use
     * @param ArrayObject $data Associative array attribute to value
     * @return array
     */
    protected function _customFilter(array $allowedAttributes, ArrayObject $data)
    {
        foreach ($data as $attribute => $value) {
            if (!in_array($attribute, $allowedAttributes)) {
                $data->offsetUnset($attribute);
            }
        }
        return $data->getArrayCopy();
    }
} 