<?php

/**
 * Os.php
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
 * Mageflow_Connect_Model_System_Info_Os
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_System_Info_Os extends Mageflow_Connect_Model_Abstract
{

    const OS_OSX = 'osx';
    const OS_LINUX = 'linux';

    /**
     * os type
     *
     * @var
     */
    private $_osType;


    /**
     * Detects and returns OS type
     *
     * @return string OS Type
     */
    public function getOsType()
    {
        if (is_null($this->_osType)) {
            switch (php_uname('s')) {
                case 'Darwin':
                    $this->_osType = self::OS_OSX;
                    break;
                case 'Linux':
                    $this->_osType = self::OS_LINUX;
                    break;
                default:
                    $this->_osType = 'N/A';
                    break;
            }
        }
        return $this->_osType;
    }

}
