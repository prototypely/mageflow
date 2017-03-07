<?php

/**
 * Datacleaner.php
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
 * Mageflow_Connect_Model_Async_Datacleaner
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 *
 */
class Mageflow_Connect_Model_Async_Datacleaner extends Mageflow_Connect_Model_Abstract
{

    /**
     * Public interface to cron functions is run()
     * This method cleans collected memory and CPU usage records that are older than one week.
     */
    public function run()
    {
        if (Mage::app()->getStore()->getConfig(Mageflow_Connect_Model_System_Config::AUTO_CLEAN_COLLECTED_MEMORY)) {
            $this->log(sprintf('Auto cleaning collected memory and CPU usage info older than %s days',
                Mageflow_Connect_Model_System_Info_Performance::OLD_DATA_AGE));
            $performanceHistoryModel = Mage::getModel('mageflow_connect/system_info_performance');
            $performanceHistoryModel->cleanOldRecords();
        }
        return true;
    }
} 