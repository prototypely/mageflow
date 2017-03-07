<?php

/**
 * Termsandconditions.php
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
 * Mageflow_Connect_Model_Handler_Sales_Termsandconditions
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_Sales_Termsandconditions
    extends Mageflow_Connect_Model_Handler_Abstract
{
    /**
     * update or create from data array
     *
     * @param $data
     *
     * @return array
     */
    public function processData(array $data)
    {
        $data = isset($data[0]) ? $data[0] : $data;

        $model = null;
        $message = 'success';
        $savedEntity = null;

        $modelByMfGuid = Mage::getModel('checkout/agreement')
            ->load($data['mf_guid'], 'mf_guid');

        if ($modelByMfGuid->getAgreementId()) {
            $model = $modelByMfGuid;
        }

        if (null === $model) {
            $model = Mage::getModel('checkout/agreement');
        }

        if ($model->getAgreementId()>0) {
            $data['agreement_id'] = $model->getAgreementId();
        }

        if (isset($data['stores'])) {
            foreach ($data['stores'] as $key => $storeCode) {
                $data['stores'][$key] = Mage::getModel('core/store')->load($storeCode, 'code')->getStoreId();
            }
        }

        try {
            $savedEntity = $this->saveItem($model, $data);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->log($e->getMessage());
            $this->log($e->getTraceAsString());
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
        $model->load($model->getAgreementId());
        $c = $this->packModel($model);
        foreach ($model->getData('store_id') as $storeId) {
            $c->stores[] = Mage::getModel('core/store')->load($storeId)->getCode();
        }
        return $c;
    }

    /**
     * @param Mageflow_Connect_Model_Interfaces_Changeitem $row
     * @return string|void
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