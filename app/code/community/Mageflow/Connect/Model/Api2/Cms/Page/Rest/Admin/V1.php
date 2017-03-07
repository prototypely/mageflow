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
 * Mageflow_Connect_Model_Api2_Cms_Page_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Cms_Page_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'cms_page';


    /**
     * GET request to retrieve a single CMS page
     *
     * @return array|mixed
     */
    public function _retrieve()
    {
        $this->log(print_r($this->getRequest()->getParams(), true));
        $storeId = $this->getRequest()->getParam('store', -1);
        $this->log($storeId);
        /**
         * @var Mage_Cms_Model_Page $workingModel
         */
        $workingModel = $this->getWorkingModel();

        $key = $this->getRequest()->getParam('key');
        $pageId = (int)$workingModel->checkIdentifier(
            $key,
            $storeId
        );

        $out = array();
        if ($pageId < 1) {
            /**
             * @var Mage_Cms_Model_Resource_Page_Collection $collection
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
            $page = $collection->getFirstItem();
        } else {
            $page = $this->getWorkingModel()->load($pageId);
        }
        if ($page instanceof Mage_Cms_Model_Page) {
            $out[] = $this->packModel($page);
        }

        return $out;
    }

    /**
     * DELETE to delete a collection of pages
     *
     * @param array $filteredData
     */
    public function _multiDelete(array $filteredData)
    {
        $this->log($filteredData);

        $pageEntity = Mage::getModel('cms/page')
            ->load($filteredData['mf_guid'], 'mf_guid');

        $originalData = $pageEntity->getData();
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
            $pageEntity->delete();
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
