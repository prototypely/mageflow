<?php

/**
 * Info.php
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
 * Mageflow_Connect_Model_System_Info
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_System_Info extends Varien_Object
{

    const PERFORMANCE_HISTORY_DISPLAY_ITEMS = 10;

    /**
     * Class constructor
     *
     * @return Mageflow_Connect_Model_System_Info
     */
    public function __construct()
    {
        return parent::__construct();
    }

    /**
     * Returns array with request/memory/cpu/sessions history
     *
     * @return array
     */
    public function getPerformanceHistory()
    {
        $memoryUsageModelCollection = Mage::getModel(
            'mageflow_connect/system_info_performance'
        )
            ->getCollection()->setPageSize(
                self::PERFORMANCE_HISTORY_DISPLAY_ITEMS
            );
        $memoryUsageModelCollection->addOrder('created_at', 'DESC');
        $out = $memoryUsageModelCollection->toArray();
        return $out['items'];
    }

}
