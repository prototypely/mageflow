<?php

/**
 * Design.php
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
 * Mageflow_Connect_Model_Handler_System_Design
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_System_Design extends Mageflow_Connect_Model_Handler_Abstract
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

        if (isset($data['store'])) {
            $storeIdList = $this->getStoreIdListByCodes(array($data['store']));
            $data['store_id'] = implode('',$storeIdList);
            unset($data['store']);
        } else {
            throw new Exception('no matching stores');
        }

        $modelByIdentifier = Mage::getModel('core/design')
            ->load($data['store'], 'store_id');

        $modelByMfGuid = Mage::getModel('core/design')
            ->load($data['mf_guid'], 'mf_guid');

        if ($modelByIdentifier->getDesignChangeId()) {
            $model = $modelByIdentifier;
        }
        if ($modelByMfGuid->getDesignChangeId()) {
            $model = $modelByMfGuid;
        }

        if (null === $model) {
            $model = Mage::getModel('core/design');
        }

        if ($model->getId()>0) {
            $data['design_change_id'] = $model->getId();
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
        $storeId = $model->getData('store_id');
        $storeEntity = Mage::getModel('core/store')->load($storeId);
        $c->store = $storeEntity->getCode();
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
        if ($object->design) {
            $out = $object->design;
        }
        return $out;
    }
} 