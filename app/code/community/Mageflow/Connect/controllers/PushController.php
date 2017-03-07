<?php


/**
 * PushController.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Controller
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_PushController
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Controller
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_PushController extends Mageflow_Connect_Controller_AbstractController {

	/**
	 * Index action
	 */
	public function indexAction() {
		return $this->composeView( 'mageflow_connect/adminhtml_push_type_page' );
	}

//    public function indexAction()
//    {
//        $this->loadLayout();
//        $this->renderLayout();
//    }
	/**
	 * this function actually makes the view
	 * shall be updated when grid loeading is updated to ajax
	 *
	 * @param type $gridBlockName
	 */
	protected function composeView( $gridBlockName, $type = 'cmspages' ) {
		$this->loadLayout();

		$isAjax = $this->getRequest()->getParam( 'isAjax', false );

		if ( $isAjax ) {
			$this->getResponse()->setBody(
				$this->getLayout()->createBlock( $gridBlockName, 'mageflow_connect.pushgrid' )->toHtml()
			);
		} else {
			$tabBlock = $this->getLayout()->createBlock(
				'mageflow_connect/adminhtml_push_tabs', 'mageflow_connect.tabs'
			);
			$this->log( $type );
			$tabBlock->setActiveTab( $type );

			$gridBlock = $this->getLayout()
			                  ->createBlock( $gridBlockName, 'mageflow_connect.pushgrid' );

			$this->_addLeft( $tabBlock );
			$this->_addContent( $gridBlock );
			$this->renderLayout();
		}
	}

	/**
	 * Pushes changesets to MageFlow
	 */
	public function pushAction() {
		$params = $this->getRequest()->getParams();
		$this->log( $params );

		$idList = $this->getRequest()->getParam( 'id', array() );
		$idArr  = array();
		if ( is_scalar( $idList ) ) {
			$idArr[] = $idList;
		} else {
			$idArr = $idList;
		}
		$changesetItemList = Mage::getModel( 'mageflow_connect/changeset_item' )
		                         ->getCollection()
		                         ->addFieldToFilter(
			                         'id', array(
				                         'in' => $idArr
			                         )
		                         );

		/**
		 * add changeset items to changeset
		 * get client
		 * client-> send changeset
		 */
		$itemData = array();
		foreach ( $changesetItemList as $changesetItem ) {

			$dataItem = array(
				'type'     => str_replace(
					array( '::', ':' ), '/', $changesetItem->getType()
				),
				'content'  => $changesetItem->getContent(),
				'encoding' => $changesetItem->getEncoding(),
			);
			if ( $changesetItem->getMetainfo() ) {
				$dataItem['meta_info'] = $changesetItem->getMetainfo();
			} else {
				$dataItem['meta_info'] = array();
			}
			$itemData[] = $dataItem;
		}
//		$company = Mage::app()->getStore()->getConfig(
//			\Mageflow_Connect_Model_System_Config::API_COMPANY
//		);
		$data    = array(
//            'company' => $company,
'instance'    => Mage::app()->getStore()->getConfig(
	\Mageflow_Connect_Model_System_Config::API_INSTANCE_KEY
),
'description' => $this->getRequest()->getParam( 'comment' ),
'items'       => $itemData,
		);

		$client = $this->getApiClient();

		$this->log( $data );

		//NOTE we need to use ARRAY here because ... Because of Magento's logic of handling single items
		/**
		 * @var Zend_Http_Response $response
		 */
		$response = $client->post( 'changeset', array( $data ) );

		$this->log( $response );

		foreach ( $changesetItemList as $changesetItem ) {
			if ( $response->isSuccessful() ) {
				$changesetItem->setStatus(
					Mageflow_Connect_Model_Changeset_Item::STATUS_SENT
				);
			} else {
				$changesetItem->setStatus(
					Mageflow_Connect_Model_Changeset_Item::STATUS_FAILED
				);
			}

			$changesetItem->setUpdatedAt( now() );
			$changesetItem->save();
		}
		$redirect = $this->getRequest()->getParam( 'redirect', 'index' );
		$this->_redirect( '*/*/' . $redirect );
	}

	/**
	 * Apply changeset
	 */
	public function applyAction() {
		$params = $this->getRequest()->getParams();
		$this->log( $params );

		$idList = $this->getRequest()->getParam( 'id', array() );
		$idArr  = array();
		if ( is_scalar( $idList ) ) {
			$idArr[] = $idList;
		} else {
			$idArr = $idList;
		}
		$changesetItemList = Mage::getModel( 'mageflow_connect/changeset_item' )
		                         ->getCollection()
		                         ->addFieldToFilter(
			                         'id', array(
				                         'in' => $idArr
			                         )
		                         );
		$this->log( count( $changesetItemList ) );

		foreach ( $changesetItemList as $changesetItem ) {
			$filteredData = json_decode( $changesetItem->getContent(), true );

			$typeName = str_replace( ':', '_', $changesetItem->getType() );

			$this->getDataProcessor( $typeName )->processData( $filteredData );
		}

		$redirect = $this->getRequest()->getParam( 'redirect', 'index' );
		$this->_redirect( '*/*/' . $redirect );
	}

	/**
	 * Grid action
	 */
	public function gridAction() {
		$this->loadLayout();
		$this->renderLayout();
		/*
		  $this->log($this->getRequest()->getParams());
		  $this->loadLayout();
		  $contentBlock = $this->getLayout()->createBlock(
		  'mageflow_connect/adminhtml_push_grid'
		  );
		  $this->getResponse()->setBody(
		  $contentBlock->toHtml()
		  );
		 *
		 */
	}

	/**
	 * Discards changesets
	 */
	public function discardAction() {
		$idList = $this->getRequest()->getParam( 'id', array() );
		$idArr  = array();
		if ( is_scalar( $idList ) ) {
			$idArr[] = $idList;
		} else {
			$idArr = $idList;
		}

		$itemMfGuidList = array();
		foreach ( $idArr as $id ) {
			$changesetItem = Mage::getModel( 'mageflow_connect/changeset_item' )
			                     ->load( $id );
			if ( $changesetItem->getIsCurrent() ) {
				$itemMfGuid = $changesetItem->getItemMfGuid();
				$changesetItem->delete();
				$changesetItemNewCurrent = Mage::getModel( 'mageflow_connect/changeset_item' )
				                               ->getCollection()->setOrder( 'id', 'DESC' )
				                               ->addFilter( 'item_mf_guid', $itemMfGuid )->getFirstItem();
				$this->log( $changesetItemNewCurrent->getId() );
				$changesetItemNewCurrent->setIsCurrent( true );
				$changesetItemNewCurrent->save();
			}
		}
		$redirect = $this->getRequest()->getParam( 'redirect', 'index' );
		$this->_redirect( '*/*/' . $redirect );
	}

	/**
	 * Flushes all changesets (truncates table)
	 */
	public function flushAction() {
		/**
		 * @var Mageflow_Connect_Model_Resource_Changeset_Item_Collection
		 * $collection
		 */
		Mage::getResourceModel( 'mageflow_connect/changeset_item' )->truncate();
		$redirect = $this->getRequest()->getParam( 'redirect', 'index' );
		$this->_redirect( '*/*/' . $redirect );
	}

	/**
	 * Refreshes media index
	 */
	public function refreshMediaIndexAction() {
		/**
		 * @var Mageflow_Connect_Helper_Media $mediaIndexHelper
		 */
		$mediaIndexHelper = Mage::helper( 'mageflow_connect/media' );
		$mediaIndexHelper->refreshIndex( true );

		$jsonData = Mage::helper( 'core' )->jsonEncode( array() );
		$this->getResponse()->setHeader( 'Content-Type', 'application/json', true );
		$this->getResponse()->setBody( $jsonData );

		return;
	}

	/**
	 * returns grid
	 */
	public function cmspagesAction() {
		return $this->composeView( 'mageflow_connect/adminhtml_push_type_page', 'cmspages' );
	}

	/**
	 * returns grid
	 */
	public function cmsblocksAction() {
		return $this->composeView( 'mageflow_connect/adminhtml_push_type_block', 'cmsblocks' );
	}

	/**
	 * returns grid
	 */
	public function cmswidgetsAction() {
		return $this->composeView( 'mageflow_connect/adminhtml_push_type_widget', 'cmswidgets' );
	}

	/**
	 * returns grid
	 */
	public function cmspollsAction() {
		return $this->composeView( 'mageflow_connect/adminhtml_push_type_poll', 'cmspolls' );
	}

	/**
	 * returns grid
	 */
	public function promotionscatalogAction() {
		return $this->composeView( 'mageflow_connect/adminhtml_push_type_promotioncatalog', 'promotionscatalog' );
	}

	/**
	 * returns grid
	 */
	public function promotionscartAction() {
		return $this->composeView( 'mageflow_connect/adminhtml_push_type_promotioncart', 'promotionscart' );
	}

	/**
	 * returns grid
	 */
	public function catalogcategoriesAction() {
		return $this->composeView( 'mageflow_connect/adminhtml_push_type_catalogcategory', 'catalogcategories' );
	}

	/**
	 * returns grid
	 */
	public function catalogattributesAction() {
		return $this->composeView( 'mageflow_connect/adminhtml_push_type_catalogattribute', 'catalogattributes' );
	}

	/**
	 * returns grid
	 */
	public function catalogattributesetsAction() {
		return $this->composeView( 'mageflow_connect/adminhtml_push_type_catalogattributeset', 'catalogattributesets' );
	}

	/**
	 * returns grid
	 */
	public function catalogproductsAction() {
		return $this->composeView( 'mageflow_connect/adminhtml_push_type_catalogproduct', 'catalogproducts' );
	}

	/**
	 * returns grid
	 */
	public function configurationAction() {
		return $this->composeView( 'mageflow_connect/adminhtml_push_type_configuration', 'configuration' );
	}

	/**
	 * returns grid
	 */
	public function otherAction() {
		return $this->composeView( 'mageflow_connect/adminhtml_push_type_other', 'other' );
	}
}
