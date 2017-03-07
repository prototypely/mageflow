<?php

/**
 * Module.php
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

namespace Mageflow\Connect;

define( 'MODULEROOT', __DIR__ );

/**
 * Module
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Lib
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
final class Module {

	/**
	 * Class constructor
	 *
	 * @return Module
	 */
	public function __construct() {
		$this->registerAutoloader();
		$this->registerModule();

		return $this;
	}

	/**
	 * register module
	 */
	private function registerModule() {
		global $moduleRegistry;
		if ( ! isset( $moduleRegistry ) ) {
			$moduleRegistry              = array();
			$moduleRegistry[ __CLASS__ ] = __DIR__;
		}
	}

	/**
	 * register autoloader
	 */
	private function registerAutoloader() {
		spl_autoload_register( array( $this, 'autoload' ), true, true );
	}

	/**
	 * Simple autoloader for Zend2-like module
	 *
	 * @param string $className
	 */
	private function autoload( $className ) {
		if ( stristr( $className, 'Mageflow\\' ) ) {
			$classPath
				       = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR
				         . str_replace(
					         '\\',
					         DIRECTORY_SEPARATOR,
					         $className
				         ) . '.php';
			$classPath = str_replace( 'src' . DIRECTORY_SEPARATOR . 'Mageflow' . DIRECTORY_SEPARATOR . 'Connect', 'src',
				$classPath );
			include_once $classPath;
		}
	}

	/**
	 * Return module's config as array
	 *
	 * @return array
	 */
	public function getConfig() {
		return include dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'module.config.php';
	}

}
