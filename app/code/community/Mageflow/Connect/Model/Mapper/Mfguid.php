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
 * Mageflow_Connect_Model_Mapper_Mfguid
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Mapper_Mfguid extends Mageflow_Connect_Model_Mapper_Base
{
    /**
     * Abstract, dump implementation of mapper
     * @param mixed $fromValue
     * @param object $context
     * @return mixed
     */
    public function mapValue($fromValue, $context = null)
    {
        $toValue = $fromValue;
        return parent::mapValue($toValue, $context);
    }
} 