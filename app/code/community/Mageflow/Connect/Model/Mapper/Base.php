<?php

/**
 * Base.php
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
 * Mageflow_Connect_Model_Mapper_Base
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Mapper_Base extends Mageflow_Connect_Model_Abstract {
	/**
	 * Abstract, dumb implementation of mapper
	 *
	 * @param        $fromValue
	 * @param object $context
	 *
	 * @return mixed
	 */
	public function mapValue( $fromValue, $context = null ) {
		$toValue = $fromValue;

		return $this->applyDefaultMappings( $toValue, $context );
	}

	/**
	 * Replace NULLs with empty strings
	 *
	 * @param        $fromValue
	 * @param object $context
	 *
	 * @return string
	 */
	public function applyDefaultMappings( $fromValue, $context = null ) {
		return ( null === $fromValue ) ? '' : $fromValue;
	}
} 