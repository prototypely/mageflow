<?php

/**
 * Attributegroup.php
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
 * Mageflow_Connect_Model_Handler_Catalog_Attributegroup
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_Catalog_Attributegroup
    extends Mageflow_Connect_Model_Handler_Abstract
{
    /**
     *
     * @param array $filteredData
     * @internal param $data
     *
     * @return array|null
     */
    public function processData(array $filteredData)
    {
        $data = isset($filteredData[0]) ? $filteredData[0] : $filteredData;
        $model = null;
        $originalEntity = null;
        $message = null;
        if (isset($data['mf_guid'])) {
            $collection = $this->getEntityCollection(array('main_table.mf_guid' => array('eq' => $data['mf_guid'])));
            $model = $collection->getFirstItem();
            if ($model instanceof Mage_Eav_Model_Entity_Attribute_Group && $model->getId() > 0) {
                $model->setMfGuid($data['mf_guid']);
                $model->save();
                $model->setAttributeGroupName(trim($data['attribute_group_name']));
                try {

                    $savedEntity = $model->save();
                } catch (Exception $ex) {
                    $message = $ex->getMessage();
                    $this->log($ex->getMessage());
                    $this->log($ex->getTraceAsString());
                }
            }
        }
        return $this->sendProcessingResponse($savedEntity, $message);
    }

    /**
     * pack content
     *
     * @param Mage_Eav_Model_Entity_Attribute_Group $model
     *
     * @return array
     */
    public function packData(Mage_Core_Model_Abstract $model)
    {
        $c = $this->packModel($model);
        $attributeSet = Mage::getModel('eav/entity_attribute_set')->load($model->getAttributeSetId());
        $c->attribute_set_name = $attributeSet->getAttributeSetName();
        $c->attribute_set_id = $attributeSet->getMfGuid();
        return $c;
    }

    /**
     * @param Mageflow_Connect_Model_Interfaces_Changeitem $row
     * @return string
     */
    public function getPreview(Mageflow_Connect_Model_Interfaces_Changeitem $row)
    {
        $out = '';
        $content = json_decode($row->getContent());
        if (isset($content->attribute_group_name)) {
            $out = $content->attribute_group_name;
        }
        return $out;
    }

    /**
     * Returns attribute group collection that is filtered by entity type (product)
     * and optionally by other filter
     *
     * @param array $filterList
     *
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection
     */
    public function getEntityCollection($filterList = array())
    {
        /**
         * @var Mage_Eav_Model_Resource_Entity_Type_Collection $entityTypeModelCollection
         */
        $entityTypeModelCollection = Mage::getModel('eav/entity_type')
            ->getCollection()
            ->addFieldToFilter('entity_type_code', array('catalog_product'));
        /**
         * @var Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection $collection
         */
        $collection = Mage::getModel('eav/entity_attribute_group')
            ->getCollection();
        $collection->addFieldToFilter(
            'table_alias.entity_type_id',
            $entityTypeModelCollection->getFirstItem()->getId()
        );
        if (count($filterList) > 0) {
            foreach ($filterList as $field => $condition) {
                $alias = isset($condition['alias']) ? sprintf('%s.', $condition['alias']) : '';
                $collection->addFilter($alias . $field, $condition['eq'], 'and');
            }
        }

        $collection->getSelect()->join(
            array(
                'table_alias' => Mage::getModel('eav/entity_attribute_group')->getResource()->getTable('eav/attribute_set')
            ),
            'main_table.attribute_set_id=table_alias.attribute_set_id',
            array('table_alias.entity_type_id')
        );
        return $collection;
    }

    /**
     * Attribute group processing validator
     * @param Mage_Core_Model_Abstract $model
     * @return bool
     */
    public function validate(Mage_Core_Model_Abstract $model)
    {
        if (Mage::registry('processing_attribute_set') == true) {
            return false;
        }
        return true;
    }

}