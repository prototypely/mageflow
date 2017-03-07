<?php

/**
 * Generic.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Model_Handler_Generic
 *
 * Generic data handler for these cases where specific handler is not created yet. It helps
 * to avoid null exceptions. It logs a message for each non-implemented case
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 *
 */
class Mageflow_Connect_Model_Handler_Generic extends Mageflow_Connect_Model_Handler_Abstract
{

    /**
     * Data processing is process where data is being sent into API and
     * MFX creates a Magento entity based on that data
     * @param array $data
     * @return array
     */
    public function processData(array $data)
    {
        $this->log('GENERIC data handler used for processing. Check implementation!');
        return array();
    }

    /**
     * Data packing is process where Magento entity is packed into portable
     * JSON container
     *
     * @param Mage_Core_Model_Abstract $data
     * @return array
     */
    public function packData(Mage_Core_Model_Abstract $data)
    {
        $this->log('GENERIC data handler used for packing. Check implementation!');
        return array();
    }


} 