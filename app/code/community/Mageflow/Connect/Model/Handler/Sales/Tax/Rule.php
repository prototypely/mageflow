<?php

/**
 * Rule.php
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
 * Mageflow_Connect_Model_Handler_Sales_Tax_Rule
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_Sales_Tax_Rule
    extends Mageflow_Connect_Model_Handler_Abstract
{
    /**
     * @param array $data
     *
     * @return array
     * @throws Exception
     */
    public function processData(array $data)
    {
        $data = isset($data[0]) ? $data[0] : $data;

        $model = null;
        $message = 'success';
        $savedEntity = null;

        $modelByIdentifier = Mage::getModel('tax/calculation_rule')
            ->load($data['code'], 'code');

        $modelByMfGuid = Mage::getModel('tax/calculation_rule')
            ->load($data['mf_guid'], 'mf_guid');

        if ($modelByIdentifier->getTaxCalculationRuleId()) {
            $model = $modelByIdentifier;
        }
        if ($modelByMfGuid->getTaxCalculationRuleId()) {
            $model = $modelByMfGuid;
        }

        if (null === $model) {
            $model = Mage::getModel('tax/calculation_rule');
        }

        if (isset($data['mf_guid']) && $model->getTaxCalculationRuleId()>0) {
            $model->setMfGuid($data['mf_guid']);
            $model->save();
        }

        if (isset($data['calculations'])) {
            $calculationDataArray = array();
            foreach($data['calculations'] as $calculationData) {
                $taxRate = Mage::getModel('tax/calculation_rate')->load(
                    $calculationData['tax_calculation_rate'], 'code'
                );
                $customerClass = Mage::getModel('tax/class')->load(
                    $calculationData['customer_tax_class'], 'class_name'
                );
                $productClass = Mage::getModel('tax/class')->load(
                    $calculationData['product_tax_class'], 'class_name'
                );

                if ($taxRate->getTaxCalculationRateId() < 1) {
                    throw new Exception('Tax rate not found');
                }

                if ($customerClass->getClassId() < 1) {
                    throw new Exception('Customer class not found');
                }

                if ($productClass->getClassId() < 1) {
                    throw new Exception('Product class not found');
                }

                $calculationDataArray[] = array(
                    'tax_calculation_rate_id' => $taxRate->getTaxCalculationRateId(),
                    'customer_tax_class_id' => $customerClass->getClassId(),
                    'product_tax_class_id' => $productClass->getClassId()
                );
            }
            $data['calculations'] = $calculationDataArray;
        }

        $data['tax_calculation_rule_id'] = $model->getTaxCalculationRuleId();

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
        $calculationsArray = array();
        $c->titles = array();

        $calculationCollection = Mage::getModel('tax/calculation')
            ->getCollection()
            ->addFieldToFilter(
                'tax_calculation_rule_id',
                array('eq' => $model->getTaxCalculationRuleId())
            );

        foreach($calculationCollection as $calculationEntity) {
            $taxRate = Mage::getModel('tax/calculation_rate')->load(
                $calculationEntity->getData('tax_calculation_rate_id')
            );
            $customerClass = Mage::getModel('tax/class')->load(
                $calculationEntity->getData('customer_tax_class_id')
            );
            $productClass = Mage::getModel('tax/class')->load(
                $calculationEntity->getData('product_tax_class_id')
            );
            $calculationsArray[] = array(
                'tax_calculation_rate' => $taxRate->getData('code'),
                'customer_tax_class' => $customerClass->getData('class_name'),
                'product_tax_class' => $productClass->getData('class_name')
            );
        }
        $c->calculations = $calculationsArray;
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
        $calculationsDataArray = null;
        if (isset($data['calculations'])) {
            $calculationsDataArray = $data['calculations'];
        }
        unset($data['calculations']);

        $model = parent::saveItem($model, $data);
        if (is_null($calculationsDataArray)) {
            return $model;
        }

        $ruleId = $model->getTaxCalculationRuleId();

        $calculationCollection = Mage::getModel('tax/calculation')
            ->getCollection()
            ->addFieldToFilter(
                'tax_calculation_rule_id',
                array('eq' => $model->getTaxCalculationRuleId())
            );

        foreach ($calculationCollection as $calculationEntity) {
            $calculationEntity->delete();
        }

        foreach ($calculationsDataArray as $calculationsData) {
            $calculationEntity = Mage::getModel('tax/calculation');
            $calculationsData['tax_calculation_rule_id'] = $ruleId;
            $calculationEntity->setData($calculationsData);
            $calculationEntity->save();
        }

        return $model;
    }
}