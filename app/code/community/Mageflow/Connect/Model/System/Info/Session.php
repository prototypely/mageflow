<?php

/**
 * Session.php
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
 * Mageflow_Connect_Model_System_Info_Session
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_System_Info_Session extends Mageflow_Connect_Model_Abstract {

	/**
	 * Returns number of active sessions
	 *
	 * @return int
	 */
	public function getNumberOfActiveSessions() {
		if ( Mage::isInstalled() ) {
			$collection = Mage::getModel( 'log/visitor_online' )
			                  ->prepare()
			                  ->getCollection();

			return $collection->count();
		}
		return 0;
	}

}
