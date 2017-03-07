<?php

/**
 * Attributeset.php
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
 * Mageflow_Connect_Model_Handler_Catalog_Attributeset
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_Catalog_Attributeset
    extends Mageflow_Connect_Model_Handler_Abstract
{
    /**
     * Returns model collection
     *
     * @param $attributeSetName
     * @param null $entityTypeId
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection
     */
    public function getModelCollection($attributeSetName = null, $entityTypeId = null)
    {
        /**
         * @var Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection $modelCollection
         */
        $modelCollection = Mage::getModel('eav/entity_attribute_set')->getCollection();
        if (null !== $attributeSetName) {
            $modelCollection->addFieldToFilter('attribute_set_name', $attributeSetName);
        }
        if (null !== $entityTypeId) {
            $modelCollection->setEntityTypeFilter($entityTypeId);
        }
        return $modelCollection;
    }

    /**
     * create or update eav/entity_attribute_set from data array
     * all attributes used by attribute set must exist already
     * on update, pre-existing attribute groups shall
     * be deleted & new groups created
     *
     * @param array $filteredData
     * @throws Exception
     * @internal param $data
     *
     * @return array|null
     */
    public function processData(array $filteredData)
    {

        $data = isset($filteredData[0]) ? $filteredData[0] : $filteredData;

        $model = null;
        $savedEntity = null;
        $message = null;

        $modelCollection = $this->getModelCollection($data['attribute_set_name'], $data['entity_type_id']);

        $modelByIdentifier = $modelCollection->getFirstItem();

        $modelByMfGuid = Mage::getModel('eav/entity_attribute_set')->load($data['mf_guid'], 'mf_guid');

        if ($modelByIdentifier->getAttributeSetId()) {
            $model = $modelByIdentifier;
        }
        if ($modelByMfGuid->getAttributeIdSet()) {
            $model = $modelByMfGuid;
            $model->setMfGuid($data['mf_guid']);
            $model->save();
        }

        if (null === $model) {
            $this->log('case 00');
            $model = Mage::getModel('eav/entity_attribute_set');
        }

        if ($model instanceof Mage_Eav_Model_Entity_Attribute_Set) {

            // we need id for the attribute set
            if (!$model->getAttributeSetId()) {
                $attributeSetData = $data;
                $attributeSetData['groups'] = array();
                $model->setData($attributeSetData);
                $model->save();
                $model->load($data['attribute_set_name'], 'attribute_set_name');
            } else {

                $originalEntity = clone $model;

                $attributeGroupCollection = Mage::getModel(
                    'eav/entity_attribute_group'
                )
                    ->getCollection()
                    ->addFieldToFilter(
                        'attribute_set_id',
                        $model->getAttributeSetId()
                    );
                foreach ($attributeGroupCollection as $attributeGroupModel) {
                    $attributeGroupModel->delete();
                }
            }

            $attributeSetData = array(
                'groups' => array()
            );


            foreach ($data['groups'] as $group) {
                $attributeGroupModel = null;
                if (isset($group['mf_guid'])) {
                    $attributeGroupModel = Mage::getModel('eav/entity_attribute_group')->load($group['mf_guid'], 'mf_guid');
                    $attributeGroupModel->setMfGuid($group['mf_guid']);
                }
                if (null === $attributeGroupModel) {
                    $attributeSetId = $model->getAttributeSetId();
                    /**
                     * @var Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection $attributeGroupCollection
                     */
                    $attributeGroupCollection = Mage::getModel(
                        'eav/entity_attribute_group'
                    )->getCollection();

                    $attributeGroupCollection->addFilter(
                        'attribute_group_name',
                        $group['attribute_group_name']
                    )
                        ->addFilter(
                            'attribute_set_id',
                            $attributeSetId
                        );
                    /**
                     * @var Mage_Eav_Model_Entity_Attribute_Group $attributeGroupModel
                     */
                    $attributeGroupModel = $attributeGroupCollection->load()->getFirstItem();
                }
                if (null === $attributeGroupModel) {
                    $attributeGroupModel = Mage::getModel('eav/entity_attribute_group');
                }

                $groupData = $group;

                $groupData['attribute_set_id'] = $model->getAttributeSetId();
                unset($groupData['attributes']);
                unset($groupData['groups']);
                $attributeGroupModel->setData($groupData);
                $attributeGroupModel->save();

                /**
                 * Save attributes
                 */
                $groupAttributeList = array();
                foreach ($group['attributes'] as $attribute) {
//                    $attributeProcessor = $this->getDataProcessor('catalog_attribute');
//                    $attributeProcessingResponse = $attributeProcessor->processData($attribute);
//                    $attributeModel = $attributeProcessor->getCurrentEntity();
                    $attributeModel = Mage::getModel('eav/entity_attribute');
                    $entityAttribute = $attributeModel->loadByCode($model->getEntityTypeId(), $attribute['attribute_code']);

//                    $entityAttribute = Mage::getModel('eav/entity_attribute');
                    //only add found attributes. This process does not create attributes!
                    if ($entityAttribute->getId() > 0) {
                        $entityAttribute
                            ->setAttributeGroupId($attributeGroupModel->getId())
                            ->setAttributeSetId($model->getId())
                            ->setEntityTypeId($model->getEntityTypeId())
                            ->setSortOrder(0);
                        $groupAttributeList[] = $entityAttribute;
                    }
                }

                $attributeGroupModel->setAttributes($groupAttributeList);
                $attributeSetData['groups'][] = $attributeGroupModel;
            }

            $model->setGroups($attributeSetData['groups']);

            try {
                $savedEntity = $model->save();
            } catch (Exception $ex) {
                $message = $ex->getMessage();
                $this->log($ex->getMessage());
                $this->log($ex->getTraceAsString());
            }
        }

        return $this->sendProcessingResponse($model, $message);
    }

    /**
     * pack content
     *
     * @param Mage_Eav_Model_Entity_Attribute_Set $model
     *
     * @return array
     */
    public function packData(Mage_Core_Model_Abstract $model)
    {
        $attributeGroupCollection = Mage::getModel(
            'eav/entity_attribute_group'
        )->getCollection()
            ->addFieldToFilter(
                'attribute_set_id',
                $model->getAttributeSetId()
            );

        if (isset($model['groups'])) {
            unset($model['groups']);
        }
        $groups = array();

//        $this->log(print_r(Mage::registry());
        //set registry flag so that attribute packer would know to NOT save attributes
//        Mage::register('processing_attribute_set', true);

        foreach ($attributeGroupCollection as $group) {
            $attributes = array();
            $attributeList = Mage::getModel('eav/entity_attribute')
                ->getCollection()
                ->setAttributeGroupFilter(
                    $group->getAttributeGroupId()
                );

            /**
             * @var Mageflow_Connect_Model_Handler_Catalog_Attribute $attributePacker
             */
            $attributePacker = $this->getDataProcessor('catalog_attribute');
            foreach ($attributeList->getItems() as $attributeModel) {
                $attributes[] = $attributePacker->packData($attributeModel);
            }

            $groupData = $group->getData();

            $attributeGroup = Mage::getModel('eav/entity_attribute_group')
                ->load(
                    $groupData['attribute_group_id'],
                    'attribute_group_id'
                );

            $data = array();
            foreach ($attributeGroup->getData() as $key => $value) {
                if ($value !== null) {
                    $data[$key] = $value;
                }
            }

            if (isset($data['attribute_group_id'])) {
                unset($data['attribute_group_id']);
            }
            if (isset($data['attribute_set_id'])) {
                unset($data['attribute_set_id']);
            }
            if (isset($data['attributes'])) {
                unset($data['attributes']);
            }

            $data['attributes'] = $attributes;
            $groups[] = $data;
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
        $c->groups = $groups;

        unset($c->attribute_set_id);
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
        if ($content->attribute_set_name) {
            $output = $content->attribute_set_name;
        }
        return $output;
    }

    /**
     * Attribute Set is saved THREE times by Magento upon its creation.
     * Here only try to catch the last save where all the data is preset.
     *
     * @param Mage_Core_Model_Abstract $model
     * @return bool|void
     */
    public function validate(Mage_Core_Model_Abstract $model)
    {
        $groups = $model->getData('groups');
        if (null !== $groups && is_array($groups) && sizeof($groups) > 0) {
            return true;
        }
        return false;
    }

}