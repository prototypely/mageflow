<?php

/**
 * Group.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com)
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Model_Handler_Customer_Group
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com)
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_Customer_Group extends Mageflow_Connect_Model_Handler_Abstract
{
    /**
     * @param array $data
     *
     * @return array
     * @throws Exception
     */
    public function processData(array $data = array())
    {
        $data = isset($data[0]) ? $data[0] : $data;
        $savedEntity = null;
        $message = 'success';
        $model = null;

        $modelByIdentifier = Mage::getModel('customer/group')
            ->load($data['customer_group_code'], 'customer_group_code');

        $modelByMfGuid = Mage::getModel('customer/group')
            ->load($data['mf_guid'], 'mf_guid');

        if ($modelByIdentifier->getCustomerGroupId()) {
            $model = $modelByIdentifier;
        }
        if ($modelByMfGuid->getCustomerGroupId()) {
            $model = $modelByMfGuid;
        }

        $customerClass = Mage::getModel('tax/class')->load(
            $data['tax_class'], 'class_name'
        );
        if ($customerClass->getClassId() > 0) {
            unset($data['tax_class']);
            $data['tax_class_id'] = $customerClass->getClassId();
        } else {
            throw new Exception('Customer class not found');
        }

        if (null === $model) {
            $model = Mage::getModel('customer/group');
        }

        if ($model->getCustomerGroupId()) {
            $data['customer_group_id'] = $model->getCustomerGroupId();
        }

        try {
            $savedEntity = $this->saveItem($model, $data);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $this->log($ex->getMessage());
            $this->log($ex->getTraceAsString());
        }

        return $this->sendProcessingResponse($savedEntity, $message);
    }

    /**
     * @param Mage_Core_Model_Abstract $model
     *
     * @return stdClass
     */
    public function packData(Mage_Core_Model_Abstract $model)
    {
        $c = $this->packModel($model);
        $customerClass = Mage::getModel('tax/class')->load(
            $model->getData('tax_class_id')
        );
        $c->tax_class = $customerClass->getData('class_name');
        return $c;
    }

    /**
     * @param Mageflow_Connect_Model_Interfaces_Changeitem $item
     *
     * @return string
     */
    public function getPreview(Mageflow_Connect_Model_Interfaces_Changeitem $item)
    {
        $out = '';

        $object = json_decode($item->getContent());
        if ($object->customer_group_code) {
            $out = $object->customer_group_code;
        }
        return $out;
    }
} 