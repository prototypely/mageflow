<?php

/**
 * Cpu.php
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
 * Mageflow_Connect_Model_System_Info_Cpu
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_System_Info_Cpu extends Mageflow_Connect_Model_Abstract
{

    /**
     * Returns int number of CPU cores
     *
     * @return int
     */
    public function getCpuCores()
    {
        if (function_exists('exec')) {
            if (Mage::getModel('mageflow_connect/system_info_os')->getOsType()
                == Mageflow_Connect_Model_System_Info_Os::OS_OSX
            ) {
                $cmd = '/usr/sbin/sysctl -n hw.ncpu';

                $retval = exec($cmd, $out);

                $this->log($retval);

                $ret = (int)$out[0];

                $this->log($out);

                return $ret;
            } elseif (Mage::getModel('mageflow_connect/system_info_os')
                    ->getOsType()
                == Mageflow_Connect_Model_System_Info_Os::OS_LINUX
            ) {
                $cmd = '/usr/bin/nproc';

                $retval = exec($cmd, $out);

                $this->log($retval);

                $ret = (int)$out[0];

                $this->log($out);

                return $ret;
            }
        }
        return -1;
    }

    /**
     * Returns average CPU load
     * of last 5 minutes divided by
     * number of CPU cores to get "actual"
     * system load
     *
     * @return float
     */
    public function getSystemLoad()
    {
        if (function_exists('sys_getloadavg')) {
            $arr = sys_getloadavg();
            return $arr[1];
        }
        return -1;
    }

}
