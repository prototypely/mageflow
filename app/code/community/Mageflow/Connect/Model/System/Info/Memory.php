<?php

/**
 * Memory.php
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
 * Mageflow_Connect_Model_System_Info_Memory
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_System_Info_Memory extends Mageflow_Connect_Model_Abstract
{

    /**
     * Returns integer bytes of free memory
     *
     * @return int
     */
    public function getFreeMemory()
    {
        $out = 0;
        if (function_exists('exec')) {
            if (Mage::getModel('mageflow_connect/system_info_os')->getOsType()
                == Mageflow_Connect_Model_System_Info_Os::OS_OSX
            ) {
                $cmd = "/usr/bin/top -l1 | awk '/PhysMem:/ {print $6}'";

                $retval = exec($cmd, $out);

                $this->log($retval);

                $this->log($out);

                $memory = (int)$out[0] * 1024 * 1024; //convert megabytes to bytes

                $this->log($memory);
                return $memory;
            } elseif ($this->getOsType()
                == Mageflow_Connect_Model_System_Info_Os::OS_LINUX
            ) {
                $cmd = 'free';
                $retval = exec($cmd, $out);

                $this->log($retval);

                $this->log($out);

                if (isset($out[1])) {
                    $retval = preg_match(
                        '/^Mem:\s*(\d*)\s*(\d*)\s*(\d*).*/i',
                        $out[1],
                        $matches
                    );

                    $this->log($retval);

                    if (is_array($matches) && sizeof($matches) > 1) {
//                        $outarr['total'] = $matches[1];
//                        $outarr['used'] = $matches[2];
//                        $outarr['free'] = $matches[3];
                        return (int)$matches[3];
                    }
                }
            } else {
                return 0;
            }
        }
        return $out;
    }

    /**
     * Returns int bytes of total memory
     * in the machine
     *
     * @return int
     */
    public function getTotalMemory()
    {
        if (function_exists('exec')) {
            if (Mage::getModel('mageflow_connect/system_info_os')->getOsType()
                == Mageflow_Connect_Model_System_Info_Os::OS_OSX
            ) {
                $cmd = '/usr/sbin/sysctl -n hw.memsize';

                $retval = exec($cmd, $out);

                $this->log($retval);

                $this->log($out);

                $memory = (int)$out[0];

                return $memory;
            } elseif ($this->getOsType()
                == Mageflow_Connect_Model_System_Info_Os::OS_LINUX
            ) {
                return 0;
            }
        }
        return 0;
    }

}
