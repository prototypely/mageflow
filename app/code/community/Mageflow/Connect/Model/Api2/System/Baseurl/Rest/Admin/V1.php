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
 * Mageflow_Connect_Model_Api2_System_Baseurl_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_System_Baseurl_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'system_baseurl';

    /**
     * Returns array with system info
     *
     * @return array
     */
    public function _retrieve()
    {
        $out = array();
        $storeCode = $this->getRequest()->getParam('store_code', null);
        if (!is_null($storeCode)) {
            $storeModel = Mage::getModel('core/store')->load($storeCode, 'code');
            if ($storeModel instanceof Mage_Core_Model_Store) {
                $storeList = Mage::app()->getStores(true, true);
                /**
                 * @var Mage_Core_Model_Store $store
                 */
                foreach ($storeList as $code => $store) {
                    if ($store->getCode() == $storeModel->getCode()) {
                        $out[] = $this->packModel($store);
                    }
                }
            }
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
        $out = array();

        $storeList = Mage::app()->getStores(true, true);
        /**
         * @var Mage_Core_Model_Store $store
         */
        foreach ($storeList as $code => $store) {
            $out[] = $this->packModel($store);
        }

        $this->log($out);
        return $out;
    }


}
