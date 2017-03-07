<?php

/**
 * Log.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Helper_Log
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Helper_Log extends Mage_Core_Helper_Abstract {

	const MAX_LENGTH = 16000;

	/**
	 * This method writes log message to modules log file
	 * and system.log
	 *
	 * @param mixed  $message
	 * @param string $method
	 * @param string $line
	 * @param        $level
	 */
	public function log(
		$message,
		$method = null,
		$line = null,
		$level = Zend_Log::DEBUG
	) {
		$currentLevel = Mage::app()->getStore()->getConfig(
			Mageflow_Connect_Model_System_Config::API_LOG_LEVEL
		);

		$logEnabled = Mage::app()->getStore()->getConfig(
			Mageflow_Connect_Model_System_Config::API_LOG_ENABLED
		);

		if ( ! $logEnabled ) {
			return;
		}

		// presuming we use only INFO & DEBUG levels
		// if we have logging on INFO, then log only with level == INFO
		if ( $currentLevel == Zend_Log::INFO && $level != Zend_Log::INFO ) {
			return;
		}
		if ( is_null( $method ) ) {
			$method = __METHOD__;
		}
		if ( is_null( $line ) ) {
			$line = __LINE__;
		}
		if ( function_exists( 'debug_backtrace' ) ) {
			$backtrace = debug_backtrace();
			$method    = $backtrace[2]['class'] . '::' . $backtrace[2]['function'];
			$line      = $backtrace[1]['line'];
		}
		$message = print_r( $message, true );
		if ( strlen( $message ) > self::MAX_LENGTH ) {
			$message = substr( $message, 0, self::MAX_LENGTH ) . ' ...';
		}

		return Mage::log(
			sprintf( '%s(%s): %s', $method, $line, $message ),
			null,
			'mageflow.log'
		);
	}

}
