<?php

/**
 * Attribute.php
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
 * Mageflow_Connect_Model_Handler_Catalog_Attribute
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_Catalog_Attribute
    extends Mageflow_Connect_Model_Handler_Abstract

{
    private $currentEntity;

    /**
     * update or create catalog/resource_eav_attribute from data array
     *
     * @param array $filteredData
     * @internal param array $data
     *
     * @return array|null
     */
    public function processData(array $filteredData)
    {
        $message = null;

        try {


            $data = isset($filteredData[0]) ? $filteredData[0] : $filteredData;

            $modelFoundByIdentifier = false;
            $modelFoundByMfGuid = false;
            $model = null;

            /**
             * @var Mage_Eav_Model_Resource_Attribute_Collection $collectionByIdentifierCollection
             */
            $collectionByIdentifierCollection = Mage::getModel('eav/entity_attribute')
                ->getCollection()
                ->setEntityTypeFilter($data['entity_type_id'])
                ->addFilter('attribute_code', $data['attribute_code']);
            $modelByIdentifier = $collectionByIdentifierCollection->getFirstItem();

            /**
             * @var Mage_Eav_Model_Resource_Attribute_Collection $collectionByMfguidCollection
             */
            $collectionByMfguidCollection = Mage::getModel('eav/entity_attribute')
                ->getCollection()
                ->setEntityTypeFilter($data['entity_type_id'])
                ->addFilter('mf_guid', $data['mf_guid']);

            $modelByMfGuid = $collectionByMfguidCollection->getFirstItem();

            if ($modelByIdentifier->getAttributeId()) {
                $model = $modelByIdentifier;
            }
            //found model by MF GUID is always "the right one"
            if ($modelByMfGuid->getAttributeId()) {
                $model = $modelByMfGuid;
                $model->setMfGuid($data['mf_guid']);
                $model->save();
            }

            if (null === $model) {
                $this->log('case 00');
                $model = Mage::getModel('catalog/resource_eav_attribute');
            }

            unset($modelByIdentifier);
            unset($modelByMfGuid);

            $data['attribute_id'] = $model->getAttributeId();

            $originalOptionValues = array();
            $originalOptionOrder = array();
            $originalDefaults = array();

            if ($model instanceof Mage_Eav_Model_Entity_Attribute) {

                $originalDefaults = array
                (
                    'default' => array(
                        $model->getDefaultValue()
                    ),
                    'default_value' => $model->getDefaultValue(),
                    'default_value_text' => $model->getDefaultValue(),
                    'default_value_yesno' => $model->getDefaultValue(),
                    'default_value_textarea' => $model->getDefaultValue(),
                );

                $storeCollection = Mage::getModel('core/store')
                    ->getCollection()
                    ->load();

                $originalOptionCollection = Mage::getModel(
                    'eav/entity_attribute_option'
                )
                    ->getCollection()
                    ->addFieldToFilter('attribute_id', $model->getAttributeId())
                    ->load();

                foreach ($originalOptionCollection as $optionEntity) {
                    foreach ($storeCollection as $storeEntity) {
                        $valueCollection = Mage::getModel(
                            'eav/entity_attribute_option'
                        )
                            ->getCollection()
                            ->setStoreFilter($storeEntity->getStoreId())
                            ->join(
                                'attribute',
                                'attribute.attribute_id=main_table.attribute_id',
                                'attribute_code'
                            )
                            ->addFieldToFilter(
                                'main_table.option_id',
                                array('eq' => $optionEntity->getOptionId())
                            )
                            ->load();

                        foreach ($valueCollection as $value) {
                            $this->log($value->getData());
                            $originalOptionValues[$optionEntity->getOptionId()][0]
                                = $value->getDefaultValue();
                            $originalOptionValues[$optionEntity->getOptionId()][$storeEntity->getStoreId()]
                                = $value->getValue();
                            $originalOptionOrder[$optionEntity->getOptionId()]
                                = $value->getSortOrder();
                        }
                    }
                }
            }

            if (isset($data['store_labels']) && is_array($data['store_labels'])) {
                foreach ($data['store_labels'] as $key => $label) {
                    if ($key == '0') {
                        $storeEntity = Mage::app()->getStore(Mage_Core_Model_Store::ADMIN_CODE);
                    } else {
                        $storeEntity = Mage::getModel('core/store')->load($key, 'code');
                    }
                    $data['store_labels'][$storeEntity->getId()] = $label;
                    unset($data['store_labels'][$key]);
                }
            }

            $optionArray = array();

            //TODO find existing option values by MFGUID
            //and then after admin store LABEL if not found
            if (
                isset($data['option'])
                && is_array($data['option'])
                && isset($data['option']['value'])
                && is_array($data['option']['value'])
            ) {
                foreach (
                    $data['option']['value'] as $valueSetKey => $valueSet
                ) {
                    foreach ($valueSet as $key => $value) {
                        if ($key != "0") {
                            $storeEntity = Mage::getModel('core/store')
                                ->load($key, 'code');
                            $data['option']['value'][$valueSetKey][$storeEntity->getId()] = $value;
                            unset($data['option']['value'][$valueSetKey][$key]);
                        }
                    }
                }
                $optionArray = $data['option'];
                unset($data['option']);
            }


            if (count($originalOptionValues) > 0) {
                $data['option']['value'] = $originalOptionValues;
                $data['option']['order'] = $originalOptionOrder;
            }

            //remove existing option values
            /**
             * @var Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection $collection
             */
            $collection = Mage::getResourceModel(
                'eav/entity_attribute_option_collection'
            )
                ->setPositionOrder('asc')
                ->setAttributeFilter($model->getId())
                ->load();
            /**
             * @var Mage_Eav_Model_Entity_Attribute_Option $optionModel
             */
            foreach ($collection->getItems() as $optionModel) {
                $data['option']['delete'][$optionModel->getId()] = $optionModel->getId();
            }

            //merge defaults
            if (null !== $originalDefaults['default_value']) {
                $data = array_merge($data, $originalDefaults);
            }

            // start rebuilding new option values
            $i = 1;
            $mfGuidArray = array();
            if (isset($optionArray['value']) && is_array($optionArray['value'])) {
                foreach ($optionArray['value'] as $key => $optionArrayValue) {
                    $data['option']['value']['option_' . $i] = $optionArrayValue;
                    $data['option']['order']['option_' . $i] = (int)$optionArray['order'][$key];
                    $mfGuidArray[$i] = $optionArray['mf_guid'][$key];
                    $i++;
                }
            }

            $model->setData($data);
            $model->save();

            $newOptions = Mage::getResourceModel(
                'eav/entity_attribute_option_collection'
            )
                ->setAttributeFilter($model->getId())
                ->setOrder('option_id', 'ASC')
                ->load();

            // we set mf_guids to options, in the order they were created
            $i = 1;
            foreach ($newOptions as $newOption) {
                $newOption->setData('mf_guid', $mfGuidArray[$i]);
                $newOption->save();
                $i++;
            }

            $this->currentEntity = $model;

        } catch (Exception $ex) {
            $this->log($ex->getMessage());
            $this->log($ex->getTraceAsString());
        }
        return $this->sendProcessingResponse($model, $message);
    }

    /**
     * pack content
     *
     * @param \Mage_Core_Model_Abstract|\Mage_Eav_Model_Entity_Attribute $model
     *
     * @return array
     */
    public function packData(Mage_Core_Model_Abstract $model)
    {
        $storeCollection = Mage::getModel('core/store')
            ->getCollection()
            ->load();

        $storeIdArray = array(0);
        $storeCodes = array(0 => "admin");
        $optionList = array();

        /**
         * @var Mage_Core_Model_Store $storeEntity
         */
        foreach ($storeCollection as $storeEntity) {
            $storeIdArray[] = (int)$storeEntity->getStoreId();
            $storeCodes[$storeEntity->getStoreId()] = $storeEntity->getCode();
        }

        if (null != $model->getAttributeCode()) {
            try {
                $this->log('Processing attribute: ' . $model->getAttributeCode());
                if ($model->usesSource()) {
                    foreach ($storeIdArray as $storeId) {
                        $collection = Mage::getResourceModel(
                            'eav/entity_attribute_option_collection'
                        )
                            ->setPositionOrder('asc')
                            ->setAttributeFilter($model->getId())
                            ->setStoreFilter($storeId)
                            ->load();

                        foreach ($collection->getItems() as $optionModel) {
                            $optionList['order'][$optionModel->getOptionId()] = $optionModel->getSortOrder();
                            $mfGuid = $optionModel->getMfGuid();
                            if (empty($mfGuid)) {
                                $optionModel->setMfGuid(Mage::helper('mageflow_connect')->randomHash(32));
                                $optionModel->save();
                            }
                            $optionList['mf_guid'][$optionModel->getOptionId()] = $optionModel->getMfGuid();
                        }
                        $option = $collection->toOptionArray();
                        foreach ($option as $value) {
                            $optionList['value'][$value['value']][$storeCodes[$storeId]] = $value['label'];
                        }
                        $storeLabels[$storeCodes[$storeId]] = $model->getStoreLabel($storeId);
                    }
                }
            } catch (Exception $ex) {
                $this->log($ex->getMessage());
            }

            $c = new stdClass();
            /**
             * @var Mageflow_Connect_Helper_Type $typeHelper
             */
            $typeHelper = Mage::helper('mageflow_connect/type');

            $fieldList = $typeHelper->getFieldList(get_class($model));

            foreach ($fieldList as $field) {
                $value = $model->getData($field);
                if (null !== $value) {
                    $c->$field = $value;
                }
            }
            $c->option = $optionList;
            if (isset($storeLabels) && is_array($storeLabels)) {
                $c->store_labels = $storeLabels;
            }
        } else {
            $c = new stdClass();
        }

        return $c;
    }

    /**
     * @param Mageflow_Connect_Model_Interfaces_Changeitem $row
     * @return string|void
     */
    public function getPreview(Mageflow_Connect_Model_Interfaces_Changeitem $row)
    {
        $output = '';
        $content = json_decode($row->getContent());
        if ($content->attribute_code) {
            $output = $content->attribute_code;
        }
        return $output;
    }

    /**
     * Returns current entity as native Magento object
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function getCurrentEntity()
    {
        return $this->currentEntity;
    }

    /**
     * Attribute processing validator
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

    /**
     * loads model by id and returns it's mf_guid
     * there are occasions when we need to ask it more sternly
     *
     * @param Mage_Eav_Model_Entity_Attribute $model
     *
     * @return mixed
     */
    public function returnMfGuid(Mage_Eav_Model_Entity_Attribute $model)
    {
        $attributeId = $model->getAttributeId();
        $model = Mage::getModel('eav/entity_attribute')
            ->getCollection()
            ->addFilter('attribute_id', $attributeId)
            ->getFirstItem();

        $mfGuid = $model->getMfGuid();

        return $mfGuid;
    }
}