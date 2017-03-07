<?php

/**
 * Performance.php
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
 * Mageflow_Connect_Model_System_Info_Performance
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_System_Info_Performance
    extends Mage_Core_Model_Abstract
{

    const OLD_DATA_AGE = 7; //days

    /**
     * construct
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('mageflow_connect/system_info_performance');
    }

    /**
     * Deletes old records from database
     */
    public function cleanOldRecords()
    {
        $this->getResource()->clean($this);
        return $this;
    }

}
