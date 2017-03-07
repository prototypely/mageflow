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
 * Mageflow_Connect_Model_Api2_System_Configuration_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_System_Configuration_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'system_configuration';

    /**
     * Returns array with system info
     *
     * @return array
     */
    public function _retrieve()
    {
        $out = array();
        $resourceModel = $this->getWorkingModel();
        /**
         * @var Mage_Core_Model_Resource_Config_Data_Collection $itemCollection
         */
        $itemCollection = $resourceModel->getCollection();
        $path = $this->getRequest()->getParam('path', null);
        if (!is_null($path)) {
            $path = str_replace(
                ':',
                '/',
                $path
            );

            $itemCollection->addFieldToFilter(array('mf_guid','path'),
                array(
                    array(
                        'eq'=>$path
                    ),
                    array(
                        'eq'=>$path
                    )
                )
            );
        }
        $scopeId = $this->getRequest()->getParam('scope_id', null);
        if (!is_null($scopeId)) {
            $itemCollection->addFieldToFilter('scope_id', $scopeId);
        }
        $configId = $this->getRequest()->getParam('id', null);
        if (!is_null($configId)) {
            $this->log($configId);
            $itemCollection->addFieldToFilter('config_id', $configId);
        }

        $out = $this->packModelCollection($itemCollection);

        $this->log($out);
        return $out;
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
