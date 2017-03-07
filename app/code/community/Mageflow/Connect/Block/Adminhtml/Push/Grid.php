<?php

/**
 * Grid.php
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
 * Mageflow_Connect_Block_Adminhtml_Push_Grid
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_Adminhtml_Push_Grid extends Mage_Adminhtml_Block_Widget_Grid {

	/**
	 * items
	 *
	 * @var
	 */
	private $_items;

	/**
	 * type
	 *
	 * @var type
	 */
	protected $_itemType = null;

	/**
	 * urlpart
	 *
	 * @var type
	 */
	protected $_urlPart = 'grid';

	/*
	 * mapping of entity types to respectivr urls
	 * this mapping shall be updated after list on
	 * tabs/types in the backend is confirmed
	 *
	 * @var array
	 */
	protected $_urlMap = array();

	/**
	 * function to return correct version of this array
	 * to be extended in different mfx versions
	 *
	 * @return type
	 */
	protected function getTypeToUrlMap() {
		return Mage::getModel( 'mageflow_connect/system_config' )->getTypeToUrlMap();
	}

	/**
	 * function to return correct version of this array
	 * to be extended in different mfx versions
	 *
	 * @return type
	 */
	protected function getTypeListing() {
		return Mage::getModel( 'mageflow_connect/system_config' )->getTypeListing();
	}

	/**
	 * Class constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->setId( 'migrationGrid' );
		$this->_urlMap = $this->getTypeToUrlMap();
		$this->setDefaultSort( 'id' );
		$this->setDefaultDir( 'DESC' );
		$this->setSaveParametersInSession( true );
		$this->setUseAjax( true );
	}

	/**
	 * Returns item collection
	 *
	 * @return Varien_Data_Collection
	 */
	public function getItems() {
		return $this->_items;
	}

	/**
	 * prepare collection
	 *
	 * @return Mageflow_Connect_Block_Adminhtml_Push_Grid
	 */
	protected function _prepareCollection() {

		// for older PHP compatibility
		$thisCollection = $this->getCollection();
		if ( empty( $thisCollection ) ) {

			$isCurrent = $this->getRequest()->getParam( 'iscurrent', 'on' );
			/**
			 * @var Varien_Data_Collection $collection
			 */
			$collection = Mage::getModel( 'mageflow_connect/changeset_item' )
			                  ->getCollection();
			if ( $isCurrent == 'on' ) {
				$collection->addFilter( 'is_current', true );
			}
			if ( ! is_null( $this->_itemType ) && ! is_array( $this->_itemType ) ) {
				$collection->addFieldToFilter( 'type', array( 'eq' => $this->_itemType ) );
			}
			$this->setCollection( $collection );
		}

		return parent::_prepareCollection();
	}

	/**
	 * Prepare grid columns
	 *
	 * @return $this
	 */
	protected function _prepareColumns() {
		$this->addColumn(
			'id', array(
				'header' => Mage::helper( 'mageflow_connect' )->__( 'ID' ),
				'width'  => '50px',
				'index'  => 'id',
				'type'   => 'text',
			)
		);

		$this->addColumn(
			'type', array(
				'header' => Mage::helper( 'mageflow_connect' )->__( 'Type' ),
				'index'  => 'type'
			)
		);
		$this->addColumn(
			'preview', array(
				'header'   => Mage::helper( 'mageflow_connect' )->__( 'Preview' ),
				'index'    => 'preview',
				'renderer'
				           => 'Mageflow_Connect_Block_Adminhtml_Push_Grid_Column_Renderer',
				'filter'   => false,
				'sortable' => false
			)
		);
		$this->addColumn(
			'mf_guid', array(
				'header'   => Mage::helper( 'mageflow_connect' )->__( 'MF GUID' ),
				'index'    => 'mf_guid',
				'renderer' => 'Mageflow_Connect_Block_Adminhtml_Push_Grid_Column_Renderer_Mfguid',
				'filter'   => false
			)
		);
		$this->addColumn(
			'website', array(
				'header'   => Mage::helper( 'mageflow_connect' )->__( 'Website' ),
				'index'    => 'website',
				'sortable' => false
			)
		);
		$this->addColumn(
			'created_at', array(
				'header' => Mage::helper( 'mageflow_connect' )->__( 'Created at' ),
				'index'  => 'created_at'
			)
		);
		$this->addColumn(
			'store', array(
				'header'   => Mage::helper( 'mageflow_connect' )->__( 'Store View' ),
				'index'    => 'store',
				'sortable' => false
			)
		);
		$this->addColumn(
			'status', array(
			'header'   => Mage::helper( 'mageflow_connect' )->__( 'Status' ),
			'index'    => 'status',
			'sortable' => true,
			'type'     => 'options',
			'options'  => array(
				Mageflow_Connect_Model_Changeset_Item::STATUS_NEW
				=> Mageflow_Connect_Model_Changeset_Item::STATUS_NEW,
				Mageflow_Connect_Model_Changeset_Item::STATUS_SENT
				=> Mageflow_Connect_Model_Changeset_Item::STATUS_SENT,
				Mageflow_Connect_Model_Changeset_Item::STATUS_FAILED
				=> Mageflow_Connect_Model_Changeset_Item::STATUS_FAILED,
				Mageflow_Connect_Model_Changeset_Item::STATUS_REJECTED
				=> Mageflow_Connect_Model_Changeset_Item::STATUS_REJECTED
			),
		), 'frontend_label'
		);

		$this->addColumn(
			'action', array(
				'header'    => Mage::helper( 'mageflow_connect' )->__( 'Action' ),
				'type'      => 'action',
				'getter'    => 'getId',
				'actions'   => array(
					array(
						'caption' => Mage::helper( 'mageflow_connect' )->__(
							'Push'
						),
						'url'     => array(
							'base'   => '*/*/push',
							'params' => array( 'redirect' => $this->getRedirectString() )
						),
						'field'   => 'id',
					),
					array(
						'caption' => Mage::helper( 'mageflow_connect' )->__(
							'Apply'
						),
						'url'     => array(
							'base'   => '*/*/apply',
							'params' => array( 'redirect' => $this->getRedirectString() )
						),
						'field'   => 'id'
					),
					array(
						'caption' => Mage::helper( 'mageflow_connect' )->__(
							'Discard'
						),
						'url'     => array(
							'base'   => '*/*/discard',
							'params' => array( 'redirect' => $this->getRedirectString() )
						),
						'field'   => 'id'
					)
				),
				'filter'    => false,
				'sortable'  => false,
				'index'     => 'stores',
				'is_system' => true,
			)
		);

		$this->addExportType(
			'*/*/exportCsv', Mage::helper( 'customer' )->__( 'CSV' )
		);
		$this->addExportType(
			'*/*/exportXml', Mage::helper( 'customer' )->__( 'Excel XML' )
		);

		return parent::_prepareColumns();
	}

	/**
	 * Returns row url
	 *
	 * @param Varien_Object $row
	 *
	 * @return string
	 */
	public function getRowUrl( $row ) {
		$url = $this->getUrl(
			'*/*/*'
		);

		return $url;
	}

	/**
	 * get grid url
	 *
	 * @return string
	 */
	public function getGridUrl() {
		$urlPart = 'grid';
		if ( isset( $this->_urlMap[ $this->_itemType ] ) ) {
			$urlPart = $this->_urlMap[ $this->_itemType ];
		}

		//$this->log(print_r($this->_itemType, true));
		return $this->getUrl( '*/*/' . $urlPart, array( '_current' => true ) );
	}

	/**
	 * get redirection string
	 *
	 * @return string
	 */
	public function getRedirectString() {
		$redirectString = '';
		if ( isset( $this->_urlMap[ $this->_itemType ] ) ) {
			$redirectString = $this->_urlMap[ $this->_itemType ];
		}

		return $redirectString;
	}

	/**
	 * prepare massaction
	 *
	 * @return $this|Mage_Adminhtml_Block_Widget_Grid
	 */
	public function _prepareMassaction() {
		$this->setMassactionIdField( 'id' );
		$this->getMassactionBlock()->setFormFieldName( 'id' );

		$this->getMassactionBlock()->addItem(
			'push', array(
				'label'   => Mage::helper( 'mageflow_connect' )->__(
					'Push to target Magento'
				),
				'url'     => $this->getUrl( '*/*/push/redirect/' . $this->getRedirectString() ),
				'confirm' => Mage::helper( 'mageflow_connect' )->__(
					'Are you sure you want to push these objects to target Magento instance?'
				)
			)
		);
		$this->getMassactionBlock()->addItem(
			'discard', array(
				'label'   => Mage::helper( 'mageflow_connect' )->__(
					'Discard selected'
				),
				'url'     => $this->getUrl( '*/*/discard/redirect/' . $this->getRedirectString() ),
				'confirm' => Mage::helper( 'mageflow_connect' )->__(
					'Are you sure you want to discard these changesets?'
				)
			)
		);
		$this->getMassactionBlock()->addItem(
			'flush', array(
				'label'   => Mage::helper( 'mageflow_connect' )->__(
					'Flush all items'
				),
				'url'     => $this->getUrl( '*/*/flush/redirect/' . $this->getRedirectString() ),
				'confirm' => Mage::helper( 'mageflow_connect' )->__(
					'Are you sure you want to flush all items?'
				)
			)
		);

		$this->_exportTypes = array();

		return $this;
	}

	public function getMainButtonsHtml() {
		$html = parent::getMainButtonsHtml(); //get the parent class buttons

		$isCurrent = $this->getRequest()->getParam( 'iscurrent', 'on' );
		if ( $isCurrent == 'on' ) {
			$currentButton = $this->getLayout()->createBlock( 'adminhtml/widget_button' )//create the add button
			                      ->setData( array(
				'label'   => Mage::helper( 'adminhtml' )->__( 'Show all versions' ),
				'onclick' => "setLocation('" . $this->getUrl( '*/*/*' ) . "iscurrent/off')",
				'class'   => 'task'
			) )->toHtml();
		}

		if ( $isCurrent == 'off' ) {
			$currentButton = $this->getLayout()->createBlock( 'adminhtml/widget_button' )//create the add button
			                      ->setData( array(
				'label'   => Mage::helper( 'adminhtml' )->__( 'Show only current versions' ),
				'onclick' => "setLocation('" . $this->getUrl( '*/*/*' ) . "iscurrent/on')",
				'class'   => 'task'
			) )->toHtml();
		}

		return $currentButton . $html;
	}

}
