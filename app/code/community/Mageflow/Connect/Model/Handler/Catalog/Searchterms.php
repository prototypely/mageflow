<?php

/**
 * Searchterms.php
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
 * Mageflow_Connect_Model_Handler_Catalog_Searchterms
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_Catalog_Searchterms
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

        $modelByMfGuid = Mage::getModel('catalogsearch/query')
            ->load($data['mf_guid'], 'mf_guid');

        if ($modelByMfGuid->getQueryId()) {
            $model = $modelByMfGuid;
        }

        if (null === $model) {
            $model = Mage::getModel('catalogsearch/query');
        }

        if ($model->getQueryId()>0) {
            $data['query_id'] = $model->getQueryId();
        }

        if (isset($data['store_id'])) {
            $data['store_id'] = Mage::getModel('core/store')->load($data['store_id'], 'code')->getStoreId();
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
        $c->store_id = Mage::getModel('core/store')->load($c->store_id)->getCode();
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
        if ($content->query_text) {
            $output = $content->query_text;
        }
        return $output;
    }

}