<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author Sven Varkel <sven.varkel@eepohs.com>
 */
set_include_path( get_include_path() . ':' . realpath( __DIR__ . '../../../' ) );
include_once __DIR__ . '/../../../lib/Mageflow/Connect/Module.php';

$m = new \Mageflow\Connect\Module();

include_once __DIR__ . '/../../../app/Mage.php';


Mage::app();
