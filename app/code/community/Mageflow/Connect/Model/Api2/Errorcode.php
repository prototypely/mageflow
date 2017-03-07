<?php

/**
 * Errorcode.php
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
 * Mageflow_Connect_Model_Api2_Errorcode
 * ErrorCode class holds error codes for errors that may occure
 * during usage of various API resources
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Errorcode
{

    //10 series - CMS Page
    const COULD_NOT_SAVE_CMS_PAGE = 10;
    //20 series - CMS Block
    const COULD_NOT_SAVE_CMS_BLOCK = 20;
    //30 series - ...

    /**
     * error messages
     *
     * @var array
     */
    public static $errorMessages
        = array(
            self::COULD_NOT_SAVE_CMS_PAGE => 'Could not save CMS page',
            self::COULD_NOT_SAVE_CMS_BLOCK => 'Could not save CMS block'
        );
}