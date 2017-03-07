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
 * Mageflow_Connect_Model_Api2_Changeset_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Changeset_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'changeset';


    /**
     * Returns JSON array with changesets
     *
     * A single changeset can be retrieved by mf_guid
     *
     * @return json
     */
    public function _retrieve()
    {
        $out = array();
        $key = $this->getRequest()->getParam('key', null);

        if (stristr($this->getRequest()->getRequestUri(), '/recover/')) {
            return $this->recoverAction($key);
        } else {

            $modelCollection = $this->getWorkingModel()->getCollection();
            if (null !== $key) {
                $modelCollection->addFilter('mf_guid', $key);
            }
            $out = $this->packModelCollection($modelCollection);
        }

        return $out;
    }

    /**
     * This actions responds to recover request and recovers a changesetitem
     * by its mf_guid
     *
     * @param $key
     *
     * @return array|void
     * @throws Exception
     * @throws Mage_Api2_Exception
     */
    protected function recoverAction($key)
    {
        $this->log('Recovering changeset ' . $key);

        /**
         * @var Mageflow_Connect_Model_Changeset_Item $model
         */
        $model = $this->getWorkingModel()->load($key, 'mf_guid');

        if ($model->getId() > 0) {

            $filteredData = json_decode($model->getContent(), true);

            $typeName = str_replace(':', '_', $model->getType());

            $out = $this->getDataProcessor($typeName)->processData($filteredData);

            return array();

        } else {
            return $this->_critical('Requested changeset not found!', 404);
        }
    }

    /**
     * Returns list of admin users.
     *
     * @return array
     */
    public function _retrieveCollection()
    {
        return parent::_retrieveCollection();
    }

    public function _update($key)
    {
        $out = array();

        return $out;
    }

    public function _create(array $filteredData)
    {
        $this->log('incoming changeset ' . print_r($filteredData, true));
        $itemList = $filteredData['items'];
        $this->log('incoming itemList length ' . sizeof($itemList));
        $out = array();

        foreach ($itemList as $item) {
            $this->log($item);
            $itemMetaJson = $item['meta_info'];
            $itemMeta = Mage::helper('core')->jsonDecode($itemMetaJson);

            $this->log($itemMeta);

            $itemMfGuid = $itemMeta['mf_guid'];

            $changesetItem = Mage::getModel('mageflow_connect/changeset_item_cache')->load($itemMfGuid, 'mf_guid');
//            $content = Mage::helper('core')->jsonDecode($item['content']);
//            $this->log($content);
            $changesetItem->setType($item['type']);
            $changesetItem->setRemoteId($itemMeta['remote_id']);
            $changesetItem->setDescription($filteredData['description']);
            $changesetItem->setContent($item['content']);
            $changesetItem->setMfGuid($itemMfGuid);
            $changesetItem->setMetaInfo($itemMetaJson);
            $changesetItem->setCreatedBy($itemMeta['created_by']);
            $changesetItem->setCreatedAt($itemMeta['created_at']);
            $changesetItem->setStatus('');
            $changesetItem->save();
        }

        return $out;
    }

}