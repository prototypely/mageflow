<?php

/**
 * Other.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Block_Adminhtml_Push_Type_Other
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_Adminhtml_Push_Type_Other extends Mageflow_Connect_Block_Adminhtml_Push_Grid {

	/**
	 * type
	 *
	 * @var type
	 */
	protected $_itemType = 'other';

	/**
	 * prepare collection
	 * OTHER is a bit complicated type and gets special treatment
	 *
	 * @return Mageflow_Connect_Block_Adminhtml_Push_Grid
	 */
	protected function _prepareCollection() {
		$collection = Mage::getModel( 'mageflow_connect/changeset_item' )->getCollection();
		$collection->addFieldToFilter( 'type', array( 'nin' => $this->getTypeListing() ) );
		$collection->addFieldToFilter( 'is_current', array( 'eq' => true ) );
		$collection->printLogQuery( false, true );

		$this->setCollection( $collection );

		return parent::_prepareCollection();
	}

}
