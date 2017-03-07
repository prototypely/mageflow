<?php

/**
 * V1.php
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
 * Mageflow_Connect_Model_Api2_Catalog_Attribute_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Catalog_Attribute_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'catalog_attribute';


    /**
     * retrieve multiple attributes
     *
     * @return array
     */
    public function _retrieveCollection()
    {
        $out = array();

        try {

            $storeCollection = Mage::getModel('core/store')
                ->getCollection()
                ->load();

            $storeIdArray = array(0);
            $storeCodes = array(0 => 0);

            /**
             * @var Mage_Core_Model_Store $storeEntity
             */
            foreach ($storeCollection as $storeEntity) {
                $storeIdArray[] = $storeEntity->getStoreId();
                $storeCodes[$storeEntity->getStoreId()] = $storeEntity->getCode();
            }

            foreach (
                Mage::getModel('eav/entity_type')
                    ->getCollection()
                    ->addFieldToFilter(
                        'entity_type_code',
                        array('catalog_product')
                    )
                    ->load()
                as $allowedEntityType
            ) {

                $collection = $this->getWorkingModel()
                    ->getCollection()
                    ->setEntityTypeFilter($allowedEntityType);

                $key = trim($this->getRequest()->getParam('key'));
                if ($key != '') {
                    $collection->addFieldToFilter('attribute_code', $key);
                }

                $out = $this->packModelCollection($collection);
            }

            $this->log($out);
        } catch (Exception $e) {
            $this->log($out);
            $this->_error('Cannot retrieve catalog/attribute', 500);
        }

        return $out;
    }

    /**
     * retrieve single attribute
     *
     * @return array
     */
    public function _retrieve()
    {
        $key = trim($this->getRequest()->getParam('key'));
        if ($key != '') {
            $collection = $this->getWorkingModel()
                ->getCollection()
                ->setEntityTypeFilter($this->getAllowedEntityTypeIds());
            $collection->addFieldToFilter('attribute_code', $key);
        } else {
            $this->_errorMessage('Entity key not specified', 500);
            return array();
        }

        $out = $this->packModelCollection($collection);
        return $out;
    }

    private function getAllowedEntityTypeIds()
    {
        $allowedEntityTypes = Mage::getModel('eav/entity_type')
            ->getCollection()
            ->addFieldToFilter(
                'entity_type_code',
                array('catalog_product')
            );
        $out = array();
        foreach ($allowedEntityTypes->getItems() as $entityType) {
            $out[] = $entityType->getId();
        }
        return $out;
    }


    /**
     * multidelete
     *
     * @param array $filteredData
     */
    public function _multiDelete(array $filteredData)
    {
        $this->log(sprintf('%s', $filteredData));

        $attributeCollection = Mage::getModel('eav/entity_attribute')
            ->getCollection()
            ->addFieldToFilter(
                'attribute_code',
                $filteredData['attribute_code']
            )
            ->addFieldToFilter('entity_type_id', 4);
        $attributeEntity = $attributeCollection->getFirstItem();

        $originalData = $attributeEntity->getData();
        // send overwritten data to mageflow
        $rollbackFeedback = array();
        if ($originalData) {
            $rollbackFeedback = $this->sendRollback(
                str_replace('_', ':', $this->_resourceType),
                $filteredData,
                $originalData
            );
        } else {
            $this->sendJsonResponse(
                array('notice' => 'target not found or empty, mf_guid='
                    . $filteredData['mf_guid'])
            );
        }
        try {
            $attributeEntity->delete();
            $this->sendJsonResponse(
                array_merge(
                    array('message' =>
                        'target deleted, mf_guid=' . $filteredData['mf_guid']),
                    $rollbackFeedback
                )
            );
        } catch (Exception $e) {
            $this->sendJsonResponse(
                array_merge(
                    array('delete error' => $e->getMessage()),
                    $rollbackFeedback
                )
            );
        }
    }
}
