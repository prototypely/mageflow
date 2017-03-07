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
 * Mageflow_Connect_Model_Resource_System_Info_Performance
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Resource_System_Info_Performance
    extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Class constructor
     *
     * @return $this
     */
    protected function _construct()
    {
        $this->_init('mageflow_connect/performance_history', 'id');
        return $this;
    }

    /**
     * Cleans old performance history records
     *
     * @param Mageflow_Connect_Model_System_Info_Performance $object
     * @return $this
     */
    public function clean(Mageflow_Connect_Model_System_Info_Performance $object)
    {

        $writeAdapter = $this->_getWriteAdapter();

        Mage::dispatchEvent('mageflow_performance_history_clean_before', array(
            'object' => $object
        ));

        $condition = array('DATEDIFF(NOW(),created_at) > (?)' => Mageflow_Connect_Model_System_Info_Performance::OLD_DATA_AGE);

        $writeAdapter->delete($this->getTable('mageflow_connect/performance_history'), $condition);

        Mage::dispatchEvent('mageflow_performance_history_clean_after', array(
            'object' => $object
        ));

        return $this;
    }

}
