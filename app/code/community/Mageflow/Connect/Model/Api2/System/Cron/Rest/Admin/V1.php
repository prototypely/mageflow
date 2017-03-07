<?php

/**
 * V1.php
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
 * Mageflow_Connect_Model_Api2_System_Cron_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_System_Cron_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'system_cron';


    /**
     * Returns array with system info
     *
     * @return array
     */
    public function _retrieve()
    {
        $out = array();
        $this->log($this->getRequest()->getParams());
        /**
         * @var Mage_Core_Model_Config_Element $cronJobs
         */
        $cronJobs = Mage::app()->getConfig()->getNode('crontab/jobs');

        /**
         * @var Mage_Core_Model_Config_Element $job
         */
        foreach ($cronJobs->children() as $name => $job) {
            $data = get_object_vars($job->children());
            if (isset($data['name'])) {
                $data['name'] = $name;
            } else {

            }
            if (isset($data['run']) && is_object($data['run'])) {
                $run = $data['run']->asArray();
                $run = $run['model'];
            } else {
                $run = '';
            }
            if (isset($data['schedule']) && is_object($data['schedule'])) {
                $schedule = $data['schedule']->asArray();
                $schedule = $schedule['cron_expr'];
            } else {
                $schedule = '';
            }

            $out[]
                = array(
                'name' => $name,
                'run' => $run,
                'schedule' => $schedule,
            );
        }
        return $out;
    }

    /**
     * retrieve collection
     *
     * @return array
     */
    public function _retrieveCollection()
    {
        return $this->_retrieve();
    }


}