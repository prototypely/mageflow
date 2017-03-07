<?php
/**
 * bootstrap.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Lib
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

// TODO: check include path
//ini_set('include_path', ini_get('include_path'));
getcwd();

include_once __DIR__ . '/../Module.php';

$m = new \Mageflow\Connect\Module();

include_once __DIR__ . '/../../../../../../public/app/Mage.php';

Mage::app();
