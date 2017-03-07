<?php

/**
 * Orderstatus.php
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
 * Mageflow_Connect_Model_Handler_System_Orderstatus
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com)
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_System_Orderstatus extends Mageflow_Connect_Model_Handler_Abstract
{
    /**
     * @param array $data
     *
     * @return mixed
     */
    public function processData(array $data = array())
    {
        $data = isset($data[0]) ? $data[0] : $data;
        $savedEntity = null;
        $message = 'success';
        $model = null;

        if (isset($data['store_labels'])) {
            $storeList = $this->findStoresByCodes(array_keys($data['store_labels']));
            foreach ($storeList as $storeEntity) {
                $data['store_labels'][$storeEntity->getId()]
                    = $data['store_labels'][$storeEntity->getCode()];
                unset($data['store_labels'][$storeEntity->getCode()]);
            }
        }

        /**
         * @var Mage_Core_Model_Orderstatus $model
         */
        /*
         * we should use the store_id as an identifier,
         * so we would not get overlapping
         */
        $modelByIdentifier = Mage::getModel('sales/order_status')
            ->load($data['status'], 'status');

        $modelByMfGuid = Mage::getModel('sales/order_status')
            ->load($data['mf_guid'], 'mf_guid');

        if ($modelByIdentifier->getOrderstatusChangeId()) {
            $model = $modelByIdentifier;
        }
        if ($modelByMfGuid->getOrderstatusChangeId()) {
            $model = $modelByMfGuid;
        }

        if (null === $model) {
            $model = Mage::getModel('sales/order_status');
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
        if (!isset($c->store_labels)) {
            $c->store_labels = $model->getStoreLabels();
        }
        if (is_array($c->store_labels)) {
            foreach ($c->store_labels as $storeId => $label) {
                $storeEntity = Mage::getModel('core/store')->load($storeId);
                unset($c->store_labels[$storeId]);
                $c->store_labels[$storeEntity->getCode()] = $label;
            }
        }
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
        if ($object->label) {
            $out = $object->label;
        }
        return $out;
    }
} 