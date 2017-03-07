<?php

/**
 * Changeset.php
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
 * Mageflow_Connect_Helper_Changeset
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Helper_Changeset extends Mageflow_Connect_Helper_Data {
	/**
	 * create changesetitem object of type from content
	 * type must be with ":", like "cms:block"
	 * content must be array from getData()
	 *
	 * @param stdClass $type
	 * @param null     $model
	 * @param boolean  $forceValidation
	 *
	 * @return Mageflow_Connect_Model_Changeset_Item
	 */
	public function createChangesetFromItem( $type, $model = null, $forceValidation = false ) {
		$changesetItem = Mage::getModel( 'mageflow_connect/changeset_item' );

		if ( $type->short == 'tax/class' ) {
			$classType = $model->getData( 'class_type' );
			if ( $classType == 'CUSTOMER' ) {
				$type->name = 'sales_tax_class_customer';
			}
			if ( $classType == 'PRODUCT' ) {
				$type->name = 'sales_tax_class_product';
			}
		}

		if ( $type->name == 'base_url' ) {
			$this->log( $model->getData( 'path' ) );
			if (
				$model->getData( 'path' ) == Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL
				|| $model->getData( 'path' ) == Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL
			) {

				$this->updateBaseUrl( $model );

				return false;
			}
		}

		$packer = $this->getPacker( $type, $model );

		if ( $packer instanceof Mageflow_Connect_Model_Interfaces_Dataprocessor ) {

			$checkSum = $packer->calculateChecksum( $model );

			$duplicateCsItem = $changesetItem->load( $checkSum, 'checksum' );

			if ( $duplicateCsItem->getId() < 1 && ( $packer->validate( $model ) || $forceValidation === true ) ) {

				/**
				 * @var stdClass $packedModel
				 */
				$packedModel = $packer->packData( $model );

				if ( null !== $packedModel ) {

					$oldItems = Mage::getModel( 'mageflow_connect/changeset_item' )
					                ->getCollection()
					                ->addFilter( 'is_current', true )
					                ->addFilter( 'item_mf_guid', $packedModel->mf_guid );

					foreach ( $oldItems as $oldItem ) {
						$oldItem->setIsCurrent( false );
						$oldItem->save();
					}

					$type = str_replace( '_', ':', $type->name );

					$this->log( $type );

					$mfGuid = $this->randomHash( 32 );

					$encodedContent = json_encode(
						$packedModel,
						JSON_FORCE_OBJECT
					);

					$metaInfo = $this->createChangeSetItemMetaInfo( $encodedContent, $mfGuid, $model->getMfGuid(),
						$model );

					$now = new Zend_Date();
					$changesetItem->setContent( $encodedContent );
					$changesetItem->setType( $type );
					$changesetItem->setEncoding( 'json' );
					$changesetItem->setCreatedAt( $now->toString( 'c' ) );
					$changesetItem->setUpdatedAt( $now->toString( 'c' ) );
					$changesetItem->setMetainfo( $metaInfo );
					$changesetItem->setMfGuid( $mfGuid );
					$changesetItem->setChecksum( $checkSum );
					$changesetItem->setItemMfGuid( $packedModel->mf_guid );
					$changesetItem->setIsCurrent( true );

					$changesetItem->save();
				}
			}
		}

		return $changesetItem;
	}

	/**
	 * Fills in changesetitem metainfo
	 *
	 * @param string $model
	 * @param null   $csItemMfGuid
	 * @param null   $itemMfGuid
	 *
	 * @return string
	 */
	private function createChangeSetItemMetaInfo(
		$model = null, $csItemMfGuid = null, $itemMfGuid = null, $contextModel = null
	) {
		$now                         = new Zend_Date();
		$metaInfo                    = new stdClass();
		$metaInfo->unsecure_base_url = Mage::app()->getStore()->getConfig(
			Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL
		);
		$metaInfo->secure_base_url   = Mage::app()->getStore()->getConfig(
			Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL
		);
		$metaInfo->created_by        = $this->getAdminUserName();
		$metaInfo->created_at        = $now->toString( 'c' );
		$metaInfo->sha1              = sha1( $model );
		$metaInfo->mf_guid           = $csItemMfGuid;
		$metaInfo->remote_id         = $contextModel ? $contextModel->getId() : null;
		if ( null !== $itemMfGuid ) {
			$metaInfo->item_mf_guid = $itemMfGuid;
		}

		return json_encode( $metaInfo );
	}

	/**
	 * Puts base URL to MageFlow API
	 *
	 * @param Mage_Core_Model_Abstract $model
	 */
	private function updateBaseUrl( $model ) {
		$this->log( 'base_url change' );

		$company     = Mage::app()->getStore()->getConfig(
			Mageflow_Connect_Model_System_Config::API_COMPANY
		);
		$instanceKey = Mage::app()->getStore()
		                   ->getConfig(
			                   Mageflow_Connect_Model_System_Config::API_INSTANCE_KEY
		                   );

		$data = array(
			'command'               => 'change_base_url',
			'instance_key'          => $instanceKey,
			'company'               => $company,
			'web_unsecure_base_url' => $model->getData( 'value' ),
			'web_secure_base_url'   => Mage::app()->getStore()->getConfig(
				Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL
			),
		);

		$this->log( $data );

		$response = $this->getApiClient()->put( 'instance/' . $instanceKey, $data );

		$this->log( 'response ' . print_r( $response, true ) );
	}
}