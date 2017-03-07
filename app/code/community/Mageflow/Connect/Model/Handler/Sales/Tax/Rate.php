<?php

/**
 * Rate.php
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
 * Mageflow_Connect_Model_Handler_Sales_Tax_Rate
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_Sales_Tax_Rate
    extends Mageflow_Connect_Model_Handler_Abstract
{
    /**
     * update or create adminhtml/email_template from data array
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

        $modelByIdentifier = Mage::getModel('tax/calculation_rate')
            ->load($data['code'], 'code');

        $modelByMfGuid = Mage::getModel('tax/calculation_rate')
            ->load($data['mf_guid'], 'mf_guid');

        if ($modelByIdentifier->getTaxCalculationRateId()) {
            $model = $modelByIdentifier;
        }
        if ($modelByMfGuid->getTaxCalculationRateId()) {
            $model = $modelByMfGuid;
        }

        if (null === $model) {
            $model = Mage::getModel('tax/calculation_rate');
        }

        if (isset($data['mf_guid']) && $model->getTaxCalculationRateId()>0) {
            $model->setMfGuid($data['mf_guid']);
            $model->save();
        }

        $data['tax_calculation_rate_id'] = $model->getTaxCalculationRateId();

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
     * @param Mage_Adminhtml_Model_Email_Template $model
     *
     * @return stdClass
     */
    public function packData(Mage_Core_Model_Abstract $model)
    {
        $c = $this->packModel($model);
        $titleArray = array();
        $storeIdList = array();
        $c->titles = array();

        foreach ($model->getTitles() as $titleEntity) {
            $storeIdList[] =$titleEntity->getData('store_id');
            $titleArray[] = array(
                'store_id' => $titleEntity->getData('store_id'),
                'value' => $titleEntity->getData('value')
            );
        }
        $storeCodeMap = $this->getStoreCodeMap($storeIdList);

        foreach ($titleArray as $title) {
            $c->titles[$storeCodeMap[$title['store_id']]] = $title['value'];
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
        if ($content->code) {
            $output = $content->code;
        }
        return $output;
    }

    /**
     * @param Mage_Core_Model_Abstract $model
     * @param                          $data
     *
     * @return object|void
     */
    public function saveItem($model, $data)
    {
        $model = parent::saveItem($model, $data);
        if (!isset($data['titles'])) {
            return $model;
        }

        $storeCodeList = array();
        foreach($data['titles'] as $code => $title) {
            $storeCodeList[] = $code;
        }
        $storeCollection = $this->findStoresByCodes($storeCodeList);

        $titleArray = array();
        foreach($storeCollection as $storeEntity) {
            $titleArray[$storeEntity->getId()] = $data['titles'][$storeEntity->getCode()];
        }

        $model->saveTitles($titleArray);

        return $model;
    }
}