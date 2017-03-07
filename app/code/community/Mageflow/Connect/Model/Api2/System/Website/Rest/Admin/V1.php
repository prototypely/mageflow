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
 * Mageflow_Connect_Model_Api2_System_Website_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_System_Website_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'system_website';


    /**
     * Returns array with system info
     *
     * @return array
     */
    public function _retrieve()
    {
        $items = array();
        $websiteCollection = Mage::getModel('core/website')->getCollection();

        /**
         * @var Mage_Core_Model_Website $website
         */
        foreach ($websiteCollection as $website) {

            $groups = array();
            $groupCollection = Mage::getModel('core/store_group')
                ->getCollection()
                ->addFieldToFilter('website_id', $website->getWebsiteId());

            /**
             * @var Mage_Core_Model_Store_Group $group
             */
            foreach ($groupCollection as $group) {
                $stores = array();
                $storeCollection = Mage::getModel('core/store')
                    ->getCollection()
                    ->addFieldToFilter('group_id', $group->getGroupId());

                /**
                 * @var Mage_Core_Model_Store $store
                 */
                foreach ($storeCollection as $store) {
                    $storeData = $store->getData();
                    unset($storeData['store_id']);
                    unset($storeData['website_id']);
                    unset($storeData['group_id']);

                    $stores[] = $this->mapOutputTypes($storeData, $store);
                }

                $groupData = $group->getData();
                unset($groupData['website_id']);
                unset($groupData['group_id']);
                $groupData['stores'] = $stores;
                $rootCategory = Mage::getModel('catalog/category')
                    ->load($groupData['root_category_id']);
                $defaultStore = Mage::getModel('core/store')
                    ->load($groupData['default_store_id']);

                $groupData['root_category'] = $rootCategory->getUrlKey();
                $groupData['root_category_id'] = $rootCategory->getMfGuid();
                $groupData['default_store_id'] = $defaultStore->getCode();
                $groups[] = $this->mapOutputTypes($groupData, $group);
            }

            $content = $this->mapOutputTypes($website->getData(), $website);
            $content['groups'] = $groups;
            unset($content['website_id']);

            $defaultGroup = Mage::getModel('core/store_group')
                ->load($content['default_group_id']);
            $content['default_group_id'] = $defaultGroup->getName();

            $items[] = $content;
        }
        return $items;
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

}