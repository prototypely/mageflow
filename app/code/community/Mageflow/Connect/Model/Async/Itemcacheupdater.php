<?php

/**
 * Itemcacheupdarer.php
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
 * Mageflow_Connect_Model_Async_Itemcacheupdater
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 *
 */
class Mageflow_Connect_Model_Async_Itemcacheupdater extends Mageflow_Connect_Model_Abstract
{

    /**
     * Public interface to cron functions is run()
     */
    public function run()
    {
        /**
         * @var Mageflow_Connect_Helper_Oauth $oauthHelper
         */
        $oauthHelper = Mage::helper('mageflow_connect/oauth');

        if (Mage::app()->getStore()->getConfig(Mageflow_Connect_Model_System_Config::AUTO_TRUNCATE_CACHE)) {
            /**
             * Delete existing items in cache table
             */
            Mage::getModel('mageflow_connect/changeset_item_cache')->getResource()->truncate();
        }

        $response = $oauthHelper->getApiClient()->get('changesetitem',
            array(
                'days_back' => Mage::app()->getStore()->getConfig(Mageflow_Connect_Model_System_Config::API_PULL_DAYS_BACK),
                'project' => Mage::app()->getStore()->getConfig(Mageflow_Connect_Model_System_Config::API_PROJECT),
                'instance_key' => Mage::app()->getStore()->getConfig(Mageflow_Connect_Model_System_Config::API_INSTANCE_KEY),
            )
        );
        $itemCollection = json_decode($response);

        $this->log($itemCollection);

        if (is_object($itemCollection)) {

            foreach ($itemCollection->items as $item) {
                $data['id'] = $item->id;

                $this->log(sprintf('Caching changeset item with ID=%s', $item->id));

                /**
                 * @var Mageflow_Connect_Model_Changeset_Item_Cache $model
                 */
                $mfGuid = null;
                if (null !== $item->meta_info) {
                    $metaInfo = json_decode($item->meta_info);
                    if (isset($metaInfo->mf_guid)) {
                        $mfGuid = $metaInfo->mf_guid;
                    }
                }
                $now = new Zend_Date();
                $model = Mage::getModel('mageflow_connect/changeset_item_cache');

                //update an existing item, insert otherwise
                $model->load($mfGuid, 'mf_guid');

                $model->setRemoteId($item->id);
                $model->setDescription($item->description);
                $model->setContent($item->content);
                $model->setMetaInfo($item->meta_info);
                $model->setType($item->type);
                $model->setMfGuid($mfGuid);
                $model->setCreatedAt($item->created_at);
                $model->setUpdatedAt($now);
                $model->setCreatedBy($item->created_by);
                $model->save();
            }
            return true;
        }
        return false;
    }
} 