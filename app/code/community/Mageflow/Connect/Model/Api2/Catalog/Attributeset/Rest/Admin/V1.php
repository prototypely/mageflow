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
 * Mageflow_Connect_Model_Api2_Catalog_Attributeset_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Catalog_Attributeset_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'catalog_attributeset';


    /**
     * retrieve
     *
     * @return array
     */
    public function _retrieve()
    {
        $out = array();
        try {
            /**
             * @var Mage_Eav_Model_Resource_Entity_Type_Collection $entityTypeModelCollection
             */
            $entityTypeModelCollection = Mage::getModel('eav/entity_type')
                ->getCollection()
                ->addFieldToFilter('entity_type_code', array('catalog_product'));

            /**
             * @var Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection $collection
             */
            $collection = Mage::getModel('eav/entity_attribute_set')
                ->getCollection()
                ->setEntityTypeFilter(
                    $entityTypeModelCollection->getFirstItem()->getEntityTypeId()
                );
            if (($key = trim($this->getRequest()->getParam('key'))) !== '') {
                $collection->addFieldToFilter('mf_guid', $key);
            }

            $out = $this->packModelCollection($collection);
        } catch (Exception $ex) {
            $this->log($ex->getMessage());
            $this->log($ex->getTraceAsString());
        }

        if(sizeof($out)==0){
            $this->_critical('Entity not found', 404);
            return array();
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
        return $this->_retrieve();
    }


    /**
     * multidelete
     *
     * @param array $filteredData
     */
    public function _multiDelete(array $filteredData)
    {
        $this->log(sprintf('%s', $filteredData));

        $attributeSetEntity = Mage::getModel('eav/entity_attribute_set')
            ->load($filteredData['mf_guid'], 'mf_guid');

        $dummyChangeset = Mage::helper('mageflow_connect/changeset')->createChangesetFromItem(
                'Mage_Eav_Model_Entity_Attribute_Set',
                $attributeSetEntity->getData()
            );
        $dummyChangesetData = $dummyChangeset->getData();
        $originalData = json_decode(
            $dummyChangesetData['content'],
            true
        );
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
            $attributeSetEntity->delete();
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
