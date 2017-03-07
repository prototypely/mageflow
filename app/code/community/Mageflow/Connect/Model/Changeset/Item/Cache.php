<?php

/**
 * Cache.php
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
 * Mageflow_Connect_Model_Changeset_Item_Cache
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 *
 * @method setRemoteId($value)
 * @method setType($value)
 * @method setDescription($value)
 * @method setMetaInfo($value)
 * @method setContent($value)
 * @method setCreatedAt($value)
 * @method setUpdatedAt($value)
 * @method setMfGuid($value)
 *
 */
class Mageflow_Connect_Model_Changeset_Item_Cache extends Mage_Core_Model_Abstract implements Mageflow_Connect_Model_Interfaces_Changeitem
{

    /**
     * Class constructor
     */
    public function _construct()
    {
        $this->_init('mageflow_connect/changeset_item_cache');
    }

    /**
     * Finds last cache update timestamp
     * @return Zend_Date
     */
    public function getLastUpdated()
    {
        /**
         * @var Mageflow_Connect_Model_Resource_Changeset_Item_Cache_Collection $collection
         */
        $collection = $this->getCollection();
        $collection->addOrder('updated_at', 'DESC');
        $model = $collection->load()->getFirstItem();
        if ($model instanceof Mageflow_Connect_Model_Changeset_Item_Cache && $model->getId() > 0) {
            $updatedAt = $model->getUpdatedAt();
            $dateObject = new Zend_Date($updatedAt, 'YYYY-MM-dd HH:mm:ss');
            return $dateObject;
        }
        $date = new Zend_Date();
        $date->sub(Mage::app()->getStore()->getConfig(Mageflow_Connect_Model_System_Config::API_PULL_DAYS_BACK), Zend_Date::DAY);
        return $date;
    }

    /**
     * Returns MFGUID
     * @return string
     */
    public function getMfGuid()
    {
        return $this->getData('mf_guid');
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->getData('content');
    }
}
