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
 * Mageflow_Connect_Model_Api2_Catalog_Category_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Catalog_Category_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'catalog_category';


    public function _retrieve()
    {
        $key = $this->getRequest()->getParam('key', null);
        $modelCollection = $this->findCategoryByNameOrGuid($key);
        $out = $this->packModelCollection($modelCollection);
        return $out;
    }

    /**
     * retrieve collection
     *
     * @return array
     */
    public function _retrieveCollection()
    {
        $out = $this->packModelCollection($this->findCategoryByNameOrGuid());
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

        $categoryCollection = Mage::getModel('catalog/category')
            ->getCollection()
            ->addFieldToFilter('mf_guid', $filteredData['mf_guid']);
        $categoryEntity = $categoryCollection->getFirstItem();

        $groupCollection = Mage::getModel('core/store_group')
            ->getCollection()
            ->addFieldToFilter(
                'root_category_id',
                $categoryEntity->getEntityId()
            );

        $this->log($groupCollection->getSize());
        if ($groupCollection->getSize() > 0) {
            $blockingStoreNames = array();
            foreach ($groupCollection as $blockingStore) {
                $blockingStoreNames[] = $blockingStore->getName();
            }
            $this->sendJsonResponse(
                array(
                    'delete error' => 'Can not delete store root category',
                    'blocking stores' => $blockingStoreNames
                )
            );
            return;
        }
        $this->log('deletable');
        $originalData = $categoryEntity->getData();
        $rollbackFeedback = array();
        // send overwritten data to mageflow
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
            $categoryEntity->delete();
            $this->sendJsonResponse(
                array_merge(
                    array('message' =>
                        'target deleted, mf_guid=' . $filteredData['mf_guid']),
                    $rollbackFeedback
                )
            );
        } catch (Exception $e) {

            $this->log($e->getMessage());
            $this->log($e->getTraceAsString());

            $this->sendJsonResponse(
                array_merge(
                    array('delete error' => $e->getMessage()),
                    $rollbackFeedback
                )
            );
        }
    }

    /**
     * Helper method to find category by name or MFGUID
     * @param $key
     * @return object
     */
    private function findCategoryByNameOrGuid($key = null)
    {
        /**
         * @var Mage_Catalog_Model_Resource_Category_Collection $collection
         */
        $collection = $this->getWorkingModel()->getCollection();
        if (null !== $key) {
            $collection->addFieldToFilter(
                array(
                    array('attribute'=>'name','eq' => $key),
                    array('attribute'=>'mf_guid', 'eq' => $key)
                )
            );
        }
        $collection->addAttributeToSelect('*');
        return $collection;
    }

    public function _update(array $filteredData)
    {
        $key = $this->getRequest()->getParam('key', null);
        $modelCollection = $this->findCategoryByNameOrGuid($key);

        if (null !== $modelCollection->getFirstItem() && $modelCollection->getFirstItem()->getId() > 0) {
            $model = $modelCollection->getFirstItem();
            $response = $this->getDataProcessor($model)->processData($filteredData);
            if ($response['status'] == 'success') {
                $this->_successMessage('OK', 202);
            } else {
                $this->_errorMessage('An error occurred while updating entity', 409);
            }
            return;
        }

        $this->_critical('Entity not found', 404);

        return;
    }

}
