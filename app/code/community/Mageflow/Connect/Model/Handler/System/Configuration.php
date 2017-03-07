<?php

/**
 * Configuration.php
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
 * Mageflow_Connect_Model_Handler_System_Configuration
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_System_Configuration
    extends Mageflow_Connect_Model_Handler_Abstract
{

    /**
     * create or update core/config_data from data array
     *
     * @param $data
     *
     * @return array|null
     */
    public function processData(array $data)
    {

        $data = isset($data[0]) ? $data[0] : $data;

        $message = null;
        $savedEntity = null;

        switch ($data['scope']) {
            case 'default':
                $oldValue = Mage::app()->getStore()->getConfig($data['path']);
                $this->log($oldValue);
                $scopeId = 0;
                break;
            case 'websites':
                $website = Mage::getModel('core/website')->load($data['scope_id'], 'mf_guid');
                $oldValue = $website->getConfig($data['path']);
                $this->log($oldValue);
                $scopeId = $website->getWebsiteId();
                break;
            case 'stores':
                $store = Mage::getModel('core/store')->load($data['scope_id'], 'mf_guid');
                $oldValue = $store->getConfig($data['path']);
                $this->log($oldValue);
                $scopeId = $store->getStoreId();
                break;
        }

        $oldModel = $this->findConfigModel($data);

        if ($oldModel->getId() > 0) {
            //save backup
            //magento takes care of duplicates etc
            $dbModel = Mage::getModel('core/config_data');
            $dbModel->setPath($data['path']);
            $dbModel->setValue($oldValue);
            $dbModel->setScope($data['scope']);
            $dbModel->setScopeId($scopeId);
            $dbModel->save();
        }

        try {
            /**
             * @var Mage_Core_Model_Config $savedEntity
             */
            Mage::getModel('core/config')->saveConfig(
                $data['path'],
                $data['value'],
                $data['scope'],
                $scopeId
            );
            $savedEntity = $this->findConfigByKey($data['path'], $data['scope']);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->log($e->getMessage());
            $this->log($e->getTraceAsString());
        }
        return $this->sendProcessingResponse($savedEntity, $message);
    }


    /**
     * @param $path
     * @param $scope
     *
     * @internal param string $key
     * @return Mage_Core_Model_Config_Data
     */
    private function findConfigByKey($path, $scope)
    {
        /**
         * @var Mage_Core_Model_Resource_Config_Data_Collection $collection
         */
        $collection = Mage::getModel('core/config_data')->getCollection();
        $collection->addFieldToFilter('path', $path);
        $collection->addFieldToFilter('scope', $scope);
        return $collection->getFirstItem();
    }

    /**
     * @param array $params
     *
     * @return Mage_Core_Model_Config_Data
     */
    public function findConfigModel($params = array())
    {
        /**
         * @var Mage_Core_Model_Resource_Config_Data_Collection $itemCollection
         */
        $itemCollection = Mage::getModel('core/config_data')->getCollection();
        $mfGuid = $params['mf_guid'];
        if (!is_null($mfGuid)) {
            $itemCollection->addFilter('main_table.mf_guid', $mfGuid, 'OR');
        }
        $path = $params['path'];
        if (!is_null($path)) {
            $path = str_replace(
                ':',
                '/',
                $path
            );
            $itemCollection->addFieldToFilter('path', $path);
        }

        //this is store MF GUID most likely
        $scopeId = $params['scope_id'];
        if (!is_null($scopeId) && $params['scope'] == 'default') {
            $itemCollection->getSelect()->join(
                array('cw' => 'core_website'), "cw.mf_guid='" . $scopeId . "'", 'cw.website_id'
            );
        }
        $configId = $params['id'];
        if (!is_null($configId)) {
            $this->log($configId);
            $itemCollection->addFieldToFilter('config_id', $configId);
        }
        $model = $itemCollection->getFirstItem();
        return $model;
    }

    /**
     * pack content
     *
     * @param Mage_Core_Model_Config_Data|Mage_Core_Model_Abstract $model
     *
     * @return array
     */
    public function packData(Mage_Core_Model_Abstract $model)
    {
        $c = $this->packModel($model);

        if (!property_exists($c, 'created_at') && property_exists($c, 'updated_at')) {
            $c->created_at = $c->updated_at;
        }

        if ($c->scope == 'default' || $c->scope == 'websites') {
            $codeMap = $this->getWebsiteCodeMap(array($c->scope_id));
            $c->scope_id = Mage::getModel('core/website')->load($codeMap[$c->scope_id], 'code')->getMfGuid();
        } else {
            $codeMap = $this->getStoreCodeMap(array($c->scope_id));
            $c->scope_id = Mage::getModel('core/store')->load($codeMap[$c->scope_id], 'code')->getMfGuid();
        }

        return $c;
    }

    /**
     * @param Mageflow_Connect_Model_Interfaces_Changeitem $row
     *
     * @return string|void
     */
    public function getPreview(Mageflow_Connect_Model_Interfaces_Changeitem $row)
    {
        $content = json_decode($row->getContent());
        $out = '';
        if (isset($content->path)) {
            $out = sprintf(
                '%s=%s',
                $content->path,
                isset($content->value)?$content->value:null
            );
        }
        return $out;
    }

    /**
     * @param Mage_Core_Model_Abstract $model
     *
     * @return bool
     */
    public function validate(Mage_Core_Model_Abstract $model)
    {
        $defaultPath = 'default/'.$model->getPath();
        $defaultValue = (string)Mage::getConfig()->getNode($defaultPath);
        $value = $model->getValue();
        if ($model->isObjectNew() && $value!=$defaultValue) {
            return true;
        }
        if ($model->isValueChanged()) {
            return true;
        }
        return false;
    }


    /**
     * @param string $typeName
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract|object
     */
    public function getModelCollection($typeName = 'system_configuration')
    {
        $itemCollection = Mage::getModel('core/config_data')->getCollection();
        return $itemCollection;
    }
}