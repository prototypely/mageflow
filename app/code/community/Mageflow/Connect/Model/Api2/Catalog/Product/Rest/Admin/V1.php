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
 * Mageflow_Connect_Model_Api2_Catalog_Product_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Catalog_Product_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'catalog_product';


    public function _retrieve()
    {
        $out = array();

        $key = $this->getRequest()->getParam('sku', 0);
        /**
         * @var Mage_Catalog_Model_Resource_Product_Collection $modelCollection
         */
        $modelCollection = $this->getWorkingModel()->getCollection();
        $modelCollection->addFilter('sku', $key, 'or');
        $modelCollection->addFilter('mf_guid', $key, 'or');
        /**
         * @var Mage_Catalog_Model_Product $model
         */
        $model = $modelCollection->getFirstItem();
        if ($model instanceof Mage_Catalog_Model_Product && $model->getId() > 0) {
            $model->load($model->getId());
            $out[] = $this->packModel($model);
        }
        return $out;
    }


    /**
     * retrieve collection
     *
     * @return array
     */
    public function _retrieveCollection()
    {
        /**
         * @var Mage_Catalog_Model_Resource_Product_Collection $collection
         */
        $collection = $this->getWorkingModel()->getCollection()->load();
        $out = $this->packModelCollection($collection);
        return $out;
    }

    /**
     * multidelete
     *
     * @param array $filteredData
     * @return array|void
     */
    public function _multiDelete(array $filteredData)
    {
        $out = array();
        $this->log(sprintf('%s', $filteredData));
        return $out;

    }

    public function _update(array $filteredData)
    {
        $model = Mage::getModel('catalog/product');
        $dataProcessor = $this->getDataProcessor($model);
        $model = $dataProcessor->findProduct(array(
               'sku' => $filteredData['sku'],
               'mf_guid' => $filteredData['mf_guid']
            ));

        if (!is_null($model)) {
            $model->getResource()->getAttribute('media_gallery')->getBackend()->afterLoad($model);
            $response = $dataProcessor->processData($filteredData);
            if ($response['status'] == 'success') {
                $this->_successMessage('OK', 202, array('current_entity' => $response['current_entity'], 'original_entity' => $response['original_entity']));
            } else {
                $this->_errorMessage('An error occurred while updating entity', 409);
            }
            return;
        }

        $this->_errorMessage('Entity not found', 404);

        return;
    }

    public function _multiUpdate(array $filteredData)
    {
        foreach ($filteredData as $singleProductData) {
            $this->_update($singleProductData);
        }
    }

    public function _create(array $filteredData)
    {
        $sku = $this->getRequest()->getParam('sku', null);
        $model = Mage::getModel('catalog/product');
        if (null == $sku) {
            $model->getResource()->getAttribute('media_gallery')->getBackend()->afterLoad($model);
            $response = $this->getDataProcessor($model)->processData($filteredData);
            if ($response['status'] == 'success') {
                $this->_successMessage(sprintf('Successfully created or updated %s', $this->getResourceType()), 200, $response);
            } else {
                $this->_critical('An error occurred while updating entity', 500);
            }
            return;
        }

        return;
    }

}
