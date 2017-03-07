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
 * Mageflow_Connect_Model_Api2_Cms_Block_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Cms_Block_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'cms_block';


    /**
     * retrieve
     *
     * @return array
     */
    public function _retrieve()
    {
        $storeId = $this->getRequest()->getParam('store', -1);

        $key = $this->getRequest()->getParam('key');

        $out = array();
        /**
         * @var Mage_Cms_Model_Resource_Block_Collection $collection
         */
        $collection = $this->getWorkingModel()->getCollection();
        $collection->addFieldToFilter(array('mf_guid', 'identifier', 'title'),
            array(
                array(
                    'eq' => $key
                ),
                array(
                    'eq' => $key
                ),
                array(
                    'eq' => $key
                )
            )
        );
        if ($storeId > 0) {
            $collection->addStoreFilter($storeId);
        }
        $model = $collection->getFirstItem();

        if ($model instanceof Mage_Cms_Model_Block) {
            $out[] = $this->packModel($model);
        }

        return $out;
    }

    /**
     * delete entities
     *
     * @param array $filteredData
     *
     * @return array
     */
    public function _multiDelete(array $filteredData)
    {
        $this->log($filteredData);

        $blockEntity = Mage::getModel('cms/block')
            ->load($filteredData['mf_guid'], 'mf_guid');

        $originalData = $blockEntity->getData();
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
            $blockEntity->delete();
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
