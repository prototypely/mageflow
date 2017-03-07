<?php

/**
 * Website.php
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
 * Mageflow_Connect_Model_Handler_System_Website
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_System_Website
    extends Mageflow_Connect_Model_Handler_Abstract
{


    /**
     * create or update website from changeset
     * all used categories must already exist with correct mf_guid's
     *
     * @param $data
     *
     * @throws Exception
     * @return array|null
     */
    public function processData(array $data)
    {
        $categoryIdList = array();
        foreach ($data['groups'] as $group) {
            $categoryIdList[] = $group['root_category_id'];
        }
        $categoryCollection = Mage::getModel('catalog/category')
            ->getCollection();
        if (sizeof($categoryIdList) > 0) {
            $categoryCollection->addFieldToFilter('mf_guid', $categoryIdList);
        }

        $websiteEntity = Mage::getModel('core/website')
            ->load($data['code'], 'code');

        $originalEntity = null;
        if ($websiteEntity instanceof Mage_Core_Model_Website && $websiteEntity->getCode() != '') {
            $originalEntity = $websiteEntity;
        }

        $websiteEntity->setCode($data['code']);
        $websiteEntity->setName($data['name']);
        $websiteEntity->setSortOrder($data['sort_order']);
        $websiteEntity->setIsDefault($data['is_default']);
        $websiteEntity->setMfGuid($data['mf_guid']);
        $websiteEntity->save();

        $this->log(
            sprintf(
                'Saved website with ID %s',
                print_r($websiteEntity->getId(), true)
            )
        );

        foreach ($data['groups'] as $group) {

            $groupEntity = null;

            if (!empty($group['mf_guid'])) {
                $groupCollectionByMfGuid = Mage::getModel('core/store_group')
                    ->getCollection()
                    ->addFieldToFilter('mf_guid', $group['mf_guid']);

                $groupEntityByMfGuid = Mage::getModel('core/store_group')
                    ->load($groupCollectionByMfGuid->getFirstItem()->getGroupId());

                // if we found it by mf_guid, we can start using it
                if ($groupEntityByMfGuid->getGroupId() > 0) {
                    $groupEntity = $groupEntityByMfGuid;
                }
            }

            if (is_null($groupEntity)) {
                $groupCollectionByName = Mage::getModel('core/store_group')
                    ->getCollection()
                    ->addFieldToFilter('name', $group['name'])
                    ->addFieldToFilter(
                        'website_id',
                        $websiteEntity->getWebsiteId()
                    );

                $groupEntityByName = Mage::getModel('core/store_group')
                    ->load($groupCollectionByName->getFirstItem()->getGroupId());

                // found by name or a new one, we shall use it
                $groupEntity = $groupEntityByName;
            }

            $groupEntity->setName($group['name']);

            if (!empty($group['mf_guid'])) {
                $groupEntity->setMfGuid($group['mf_guid']);
            }

            $categoryCollection = Mage::getModel('catalog/category')
                ->getCollection()
                ->addFieldToFilter('mf_guid', $group['root_category_id']);
            $rootCategory = $categoryCollection->getFirstItem();
            $groupEntity->setRootCategoryId($rootCategory->getEntityId());
            $groupEntity->setWebsiteId($websiteEntity->getWebsiteId());
            $groupEntity->save();

            if ($groupEntity->getName() == $data['default_group_id']) {
                $websiteEntity->setDefaultGroupId($groupEntity->getGroupId());
                $websiteEntity->save();
            }

            foreach ($group['stores'] as $store) {
                $storeEntity = Mage::getModel('core/store')
                    ->load($store['code'], 'code');

                $storeEntity->setCode($store['code']);
                $storeEntity->setName($store['name']);
                $storeEntity->setSortOrder($store['sort_order']);
                $storeEntity->setIsActive($store['is_active']);
                $storeEntity->setWebsiteId($websiteEntity->getWebsiteId());
                $storeEntity->setGroupId($groupEntity->getGroupId());
                $storeEntity->setMfGuid($store['mf_guid']);
                $storeEntity->save();

                if ($storeEntity->getCode() == $group['default_store_id']) {
                    $groupEntity->setDefaultStoreId($storeEntity->getStoreId());
                }

            }
        }

        return $this->sendProcessingResponse($websiteEntity, $originalEntity);

    }

    /**
     * pack content
     *
     * @param $model
     *
     * @return array|mixed
     */
    public function packData(Mage_Core_Model_Abstract $model)
    {
        $website = Mage::getModel('core/website')
            ->load($model['website_id']);

        $groups = array();
        $groupCollection = Mage::getModel('core/store_group')
            ->getCollection()
            ->addFieldToFilter('website_id', $website->getWebsiteId());

        foreach ($groupCollection as $group) {
            $stores = array();
            $storeCollection = Mage::getModel('core/store')
                ->getCollection()
                ->addFieldToFilter('group_id', $group->getGroupId());

            foreach ($storeCollection as $store) {
                $storeData = $store->getData();
                unset($storeData['store_id']);
                unset($storeData['website_id']);
                unset($storeData['group_id']);

                $stores[] = $storeData;
            }

            $groupData = $group->getData();
            unset($groupData['website_id']);
            unset($groupData['group_id']);
            $groupData['stores'] = $stores;
            $rootCategory = Mage::getModel('catalog/category')
                ->load($groupData['root_category_id']);
            $defaultStore = Mage::getModel('core/store')
                ->load($groupData['default_store_id']);

            $groupData['root_category_id'] = $rootCategory->getMfGuid();
            $groupData['default_store_id'] = $defaultStore->getCode();
            $groups[] = $groupData;
        }

        $model = $website->getData();
        $model['groups'] = $groups;
        unset($model['website_id']);

        $defaultGroup = Mage::getModel('core/store_group')
            ->load($model['default_group_id']);
        $model['default_group_id'] = $defaultGroup->getName();
        return $model;
    }

    /**
     * @param Mageflow_Connect_Model_Interfaces_Changeitem $row
     * @return string
     */
    public function getPreview(Mageflow_Connect_Model_Interfaces_Changeitem $row)
    {
        $output = '';
        $content = json_decode($row->getContent());
        if ($content->name) {
            $output = $content->name;
        }
        return $output;
    }

}