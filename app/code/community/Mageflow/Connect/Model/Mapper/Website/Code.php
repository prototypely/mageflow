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
 * Mageflow_Connect_Model_Mapper_Website_Code
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Mapper_Website_Code extends Mageflow_Connect_Model_Mapper_Base
{

    /**
     * Search for store names by ID-s and replace ID-s with names for better interoperability
     *
     * @param $fromValue
     * @param object $context
     * @return mixed|void
     */
    public function mapValue($fromValue, $context = null)
    {
        $this->log($fromValue);
        $toValue = $fromValue;
        return parent::mapValue($toValue, $context);
    }
} 