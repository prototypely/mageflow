<?php

/**
 * Abstract.php
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
 * Mageflow_Connect_Model_Handler_Abstract
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 *
 * @method processData(array $data)
 * @method packData(Mage_Core_Model_Abstract $data)
 *
 */
abstract class Mageflow_Connect_Model_Handler_Abstract
    extends Mageflow_Connect_Model_Abstract implements Mageflow_Connect_Model_Interfaces_Dataprocessor
{
    private $modelInstance = null;

    /**
     * @param array $args
     */
    public function __construct($args = array())
    {
        if (isset($args['modelInstance'])) {
            $this->modelInstance = $args['modelInstance'];
        }
    }

    /**
     * Returns model instance or null
     *
     * @return Mage_Core_Model_Abstract|null
     */
    public function getModelInstance()
    {
        return $this->modelInstance;
    }

    /**
     * sets data from array and saves object
     *
     * @param Mage_Core_Model_Abstract $itemModel
     * @param                          $filteredData
     *
     * @return object
     */
    public function saveItem($itemModel, $filteredData)
    {
        if (null !== $itemModel) {

            $itemModel->setData($filteredData);
            $itemModel->save();
            $this->log(sprintf('Saved %s with id=%s', get_class($itemModel), $itemModel->getId()));
        }

        return $itemModel;
    }

    /**
     * @param Mageflow_Connect_Model_Interfaces_Changeitem $row
     *
     * @return string
     */
    public function getPreview(Mageflow_Connect_Model_Interfaces_Changeitem $row)
    {
        return 'n/a';
    }

    /**
     * @param Mageflow_Connect_Model_Interfaces_Changeitem $row
     *
     * @return string
     */
    public function getMfGuid(Mageflow_Connect_Model_Interfaces_Changeitem $row)
    {
        $mfGuid = $row->getMfGuid();
        return $mfGuid;
    }

    /**
     * Validates model before save
     *
     * @param $model
     *
     * @return bool
     */
    public function validate(Mage_Core_Model_Abstract $model)
    {
        return true;
    }


    /**
     * Sends unified processing response
     *
     * @param object $currentEntity
     * @param null $message
     *
     * @return array
     */
    protected function sendProcessingResponse($currentEntity = null, $message = null)
    {
        $out = array();
        if (!is_null($message)) {
            $out['message'] = $message;
        }
        if ($currentEntity instanceof Varien_Object || $currentEntity instanceof Varien_Simplexml_Config) {
            $out ['status'] = 'success';
            $out['current_entity'] = $this->getDataProcessor(get_class($currentEntity))->packData($currentEntity);
            if (Mage::registry($currentEntity->getMfGuid())) {
                $out['recovery_mf_guid'] = Mage::registry($currentEntity->getMfGuid());
            }
        } else {
            $this->log('Error occurred while packing current entity.');
            $out['status'] = 'error';
        }
        return $out;
    }

    /**
     * @param \string $typeName
     *
     * @return Mageflow_Connect_Model_Interfaces_Dataprocessor
     */
    protected function getDataProcessor($typeName)
    {
        /**
         * @var Mageflow_Connect_Helper_Type $typeHelper
         */
        $typeHelper = Mage::helper('mageflow_connect/type');
        $type = $typeHelper->getType($typeName);
        $dataHelper = Mage::helper('mageflow_connect');
        return $dataHelper->getPacker($type);
    }

    /**
     * Packs model as a stdClass
     *
     * @param Mage_Core_Model_Abstract $model
     *
     * @return stdClass
     */
    public function packModel($model)
    {
        $c = new stdClass();
        /**
         * @var Mageflow_Connect_Helper_Type $typeHelper
         */
        $typeHelper = Mage::helper('mageflow_connect/type');

        $fieldList = $typeHelper->getFieldList(get_class($model));

        foreach ($fieldList as $field) {
            $value = $model->getOrigData($field);
            if (null !== $value) {
                $c->$field = $value;
            }
            $value = $model->getData($field);
            if (null !== $value) {
                $c->$field = $value;
            }
        }
        return $c;
    }

    /**
     * @param $websiteIds
     *
     * @return Mage_Core_Model_Resource_Website_Collection
     */
    protected function findWebsitesByIds($websiteIds)
    {
        return $this->findModelCollectionByArrayOfValues('core/website', 'website_id', array_values($websiteIds));
    }

    /**
     * @param $websiteCodes
     *
     * @return Mage_Core_Model_Resource_Website_Collection
     */
    protected function findWebsitesByCodes($websiteCodes)
    {
        return $this->findModelCollectionByArrayOfValues('core/website', 'code', array_values($websiteCodes));
    }

    /**
     * @param $storeIds
     *
     * @return Mage_Core_Model_Resource_Store_Collection
     */
    public function findStoresByIds($storeIds)
    {
        return $this->findModelCollectionByArrayOfValues('core/store', 'store_id', array_values($storeIds));
    }

    /**
     * @param $websiteId
     *
     * @return null
     */
    public function getWebsiteCodeById($websiteId)
    {
        $websiteEntity = null;
        $websiteCode = null;
        $websiteEntity = $this->findWebsitesByIds(array($websiteId))->getFirstItem();

        if (!is_null($websiteEntity)) {
            $websiteCode = $websiteEntity->getCode();
        }

        return $websiteCode;
    }

    /**
     * @param $websiteCode
     *
     * @return mixed
     */
    public function getWebsiteIdByCode($websiteCode)
    {
        $websiteEntity = null;
        $websiteId = null;
        $websiteEntity = $this->findWebsitesByCodes(array($websiteCode))->getFirstItem();

        if (!is_null($websiteEntity)) {
            $websiteId = $websiteEntity->getWebsiteId();
        }

        return $websiteId;
    }

    /**
     * @param $customerGroupId
     *
     * @return null
     */
    public function getCustomerGroupCodeById($customerGroupId)
    {
        $customerGroupEntity = Mage::getModel('customer/group')->load($customerGroupId, 'customer_group_id');
        $customerGroupCode = null;

        if (!is_null($customerGroupEntity)) {
            $customerGroupCode = $customerGroupEntity->getCustomerGroupCode();
        }

        return $customerGroupCode;
    }

    /**
     * @param $customerGroupCode
     *
     * @return null
     */
    public function getCustomerGroupIdByCode($customerGroupCode)
    {
        $customerGroupEntity = Mage::getModel('customer/group')->load($customerGroupCode, 'customer_group_code');
        $customerGroupId = null;

        if (!is_null($customerGroupEntity)) {
            $customerGroupId = $customerGroupEntity->getCustomerGroupId();
        }

        return $customerGroupId;
    }

    /**
     * @param $storeCodes
     *
     * @return Mage_Core_Model_Resource_Store_Collection
     */
    public function findStoresByCodes($storeCodes)
    {
        return $this->findModelCollectionByArrayOfValues('core/store', 'code', array_values($storeCodes));
    }

    /**
     * Returns array with website ID list found by store codes
     *
     * @param $codeList
     *
     * @return array
     */
    public function getWebsiteIdListByCodes($codeList)
    {
        $collection = $this->findWebsitesByCodes($codeList);
        $idList = array();
        /**
         * @var Mage_Core_Model_Website $model
         */
        foreach ($collection as $model) {
            $idList[] = $model->getId();
        }
        if (sizeof($idList) == 0) {
            $idList[] = Mage::app()->getWebsite()->getId();
        }
        return $idList;
    }

    /**
     * Returns array with store ID list found by store codes
     *
     * @param $codeList
     *
     * @return array
     */
    public function getStoreIdListByCodes($codeList)
    {
        $collection = $this->findStoresByCodes($codeList);
        $idList = array();
        /**
         * @var Mage_Core_Model_Store $model
         */
        foreach ($collection as $model) {
            $idList[] = $model->getId();
        }
        return $idList;
    }

    /**
     * Returns list of stores missing in current instance
     *
     * @param $codeList
     *
     * @return string
     */
    public function getMissingStores($codeList)
    {
        $collection = $this->findStoresByCodes($codeList);
        /**
         * @var Mage_Core_Model_Store $model
         */
        foreach ($collection as $model) {
            unset($codeList[array_search($model->getCode(), $codeList)]);
        }

        return implode(', ', $codeList);
    }

    /**
     * Returns array with website codes mapped to ID-s
     *
     * @param $websiteIds
     *
     * @return array
     */
    public function getWebsiteCodeMap($websiteIds)
    {
        $modelCollection = $this->findWebsitesByIds($websiteIds);
        $codeList = array();
        /**
         * @var Mage_Core_Model_Website $website
         */
        foreach ($modelCollection as $website) {
            $codeList[$website->getId()] = $website->getCode();
        }
        return $codeList;
    }

    /**
     * Returns array with store codes mapped to ID-s
     *
     * @param $storeIds
     *
     * @return array
     */
    protected function getStoreCodeMap($storeIds)
    {
        $modelCollection = $this->findStoresByIds($storeIds);
        $codeList = array();
//        if ($storeIds[0] == "0") {
//            $key = '0';
//            $codeList->$key = 'admin';
//        }
        /**
         * @var Mage_Core_Model_Store $store
         */
        foreach ($modelCollection as $store) {
            $codeList[$store->getId()] = $store->getCode();
        }
        return $codeList;
    }

    /**
     * Returns map of store MF GUIDs
     *
     * @param array $idList
     * @return array
     */
    protected function getStoreMfGuidList(array $idList)
    {
        $modelCollection = $this->findStoresByIds($idList);
        $mfGuidList = array();
        foreach ($modelCollection as $store) {
            $mfGuidList[$store->getId()] = $store->getMfGuid();
        }
        return $mfGuidList;
    }

    /**
     * Returns map of website MF GUIDs
     *
     * @param array $idList
     * @return array
     */
    protected function getWebsiteMfGuidList(array $idList)
    {
        $modelCollection = $this->findWebsitesByIds($idList);
        $mfGuidList = array();
        foreach ($modelCollection as $model) {
            $mfGuidList[$model->getId()] = $model->getMfGuid();
        }
        return $mfGuidList;
    }


    /**
     * @param $type
     * @param $field
     * @param $values
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function findModelCollectionByArrayOfValues($type, $field, $values)
    {
        /**
         * @var Mage_Core_Model_Resource_Db_Collection_Abstract $modelCollection
         */
        $modelCollection = Mage::getModel($type)->getCollection();
        $modelCollection->setLoadDefault(true);
        $modelCollection->addFieldToFilter($field, array('in' => array_values($values)));
        $modelCollection->load();
        return $modelCollection;
    }


    /**
     * @param        $typeName
     * @param string $mfGuid
     * @param array $identifier
     *
     * @return Mage_Core_Model_Abstract
     */
    public function findModel($typeName, $mfGuid = null, $identifier = array())
    {
        $model = Mage::getModel($typeName);
        $modelFound = false;
        if (null !== $mfGuid) {
            $this->log(sprintf('Looking for %s by mf_guid=%s', $typeName, $mfGuid));
            $model = $model->load($mfGuid, 'mf_guid');
            //TODO verify admin store ID
            if ($model->getId() > 0) {
                $this->log(sprintf('Found %s by mf_guid=%s', $typeName, $mfGuid));
                $modelFound = true;
            }
        }
        if (!$modelFound && sizeof($identifier) > 0) {
            $idField = $identifier['field'];
            $idValue = $identifier['value'];
            $this->log(
                sprintf(
                    'Looking for %s by %s=%s', $typeName, $idField, $idValue
                )
            );
            //TODO verify admin store ID

            if (isset($identifier['stores'])) {
                $model = $model->getCollection()->addStoreFilter(
                    $identifier['stores']
                )->addFilter($idField, $idValue)->getFirstItem();
            } else {
                $model = $model->load($idValue, $idField);
            }
            if ($model->getId() > 0) {
                $this->log(
                    sprintf('Found %s by %s=%s', $typeName, $idField, $idValue)
                );
            }
        }

        if (null !== $mfGuid && $model->getId() > 0) {
            $model->setMfGuid($mfGuid);
            $model->save();
        }

        return $model;
    }

    /**
     * Calculates model's checksum over its significant fields
     *
     * @param Mage_Core_Model_Abstract $model
     *
     * @return string
     */
    public function calculateChecksum(Mage_Core_Model_Abstract $model)
    {
        /**
         * @var Mageflow_Connect_Helper_Type $typeHelper
         */
        $typeHelper = Mage::helper('mageflow_connect/type');

        $fieldList = $typeHelper->getSignificantFieldList(get_class($model));
        $data = array();
        //add up all significant fields
        foreach ($fieldList as $fieldName) {
            $data[] = $model->getData($fieldName);
        }

        //make it a string
        $dataStr = json_encode($data);

        return sha1($dataStr);
    }

    /**
     * Reindexes current data type
     *
     * @param stdClass $typeDef
     * @param int $limit
     *
     * @return int
     */
    public function reindex($typeDef, $limit = -1)
    {
        $count = 0;
        try {
            //TODO refactor / re-pattern the following IF
            if ($typeDef->name == 'catalog_attributeset') {
                $entityTypeId = Mage::getModel('catalog/product')->getResource()->getTypeId();
                $modelCollection = $this->getModelCollection(null, $entityTypeId);
            } elseif ($typeDef->name == 'catalog_attributegroup') {
                $modelCollection = $this->getEntityCollection();
            } else {
                $modelCollection = $this->getModelCollection($typeDef->collection);
            }

            if ($limit > 0) {
                $modelCollection->setPageSize($limit);
            }
            /**
             * @var Mageflow_Connect_Helper_Changeset $csHelper
             */
            $csHelper = Mage::helper('mageflow_connect/changeset');

            $modelCollection->load();

            foreach ($modelCollection->getItems() as $model) {
                $csHelper->createChangesetFromItem($typeDef, $model, true);
                $count++;
            }
        } catch (Exception $ex) {
            $this->log($ex->getMessage());
        }
        return $count;
    }

    /**
     * @param $typeName
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    public function getModelCollection($typeName)
    {
        $modelCollection = Mage::getModel($typeName)->getCollection();
        return $modelCollection;
    }

    /**
     * get simple id => code array of stores
     *
     * @return array
     */
    public function getStoreCodeArray()
    {
        $storeListArray = array();
        $storeCollection = Mage::getModel('core/store')->getCollection();
        foreach ($storeCollection as $storeEntity) {
            $storeListArray[$storeEntity->getStoreId()] = $storeEntity->getCode();
        }

        return $storeListArray;
    }

    /**
     * get simple code => id array of stores
     *
     * @return array
     */
    public function getStoreIdArray()
    {
        $storeListArray = array();
        $storeCollection = Mage::getModel('core/store')->getCollection();
        foreach ($storeCollection as $storeEntity) {
            $storeListArray[$storeEntity->getCode()] = $storeEntity->getStoreId();
        }

        return $storeListArray;
    }
}