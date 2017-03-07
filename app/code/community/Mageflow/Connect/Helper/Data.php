<?php

/**
 * Data.php
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
 * Mageflow_Connect_Helper_Data
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Returns MageFlow setting value from config.xml
     *
     * @param $path
     *
     * @return string
     */
    public function getSettingValue($path)
    {
        $arr = Mage::app()->getConfig()->getXpath($path);
        /**
         * @var Mage_Core_Model_Config_Element $el
         */
        $el = $arr[0];
        $value = null;
        if ($el instanceof Mage_Core_Model_Config_Element) {
            if (null != $el->getAttribute('value')) {
                $value = $el->getAttribute('value');
            } else {
                $value = (string)$el;
            }
        }
        return $value;
    }

    /**
     * Returns hash of pretty random bytes
     *
     * @param int $length
     *
     * @return string
     */
    public function randomHash($length = 32)
    {
        $helper = Mage::helper('core');

        return $helper->getRandomString(
            $length, Mage_Core_Helper_Data::CHARS_DIGITS . Mage_Core_Helper_Data::CHARS_LOWERS
        );
    }



    /**
     * Helper method to retrieve admin user name
     *
     * @return string
     */
    public function getAdminUserName()
    {
        if (
            Mage::getSingleton('admin/session')
            && Mage::getSingleton('admin/session')->getUser()
            && Mage::getSingleton('admin/session')->getUser()->getId()
        ) {
            return Mage::getSingleton('admin/session')->getUser()->getUsername();
        }
        return 'n/a';
    }



    /**
     * Packer factory that helps to get packer for given type
     *
     * @param stdClass $type
     * @param object   $instance
     *
     * @return Mage_Core_Helper_Abstract|null
     */
    public function getPacker($type, $instance = null)
    {
        /**
         * @var Mageflow_Connect_Helper_Type $typeHelper
         */
        $typeHelper = Mage::helper('mageflow_connect/type');
        $handlerClass = $typeHelper->getHandlerClass($type->name, $instance);
        if (class_exists($handlerClass, true)) {
            return Mage::getModel($handlerClass);
        }
        return null;
    }


    /**
     * get changeset collection from mf api
     *
     * @param int $limit number of days since when pull changeset items
     *
     * @return Varien_Data_Collection
     */
    public function getItemCollectionFromMfApi($limit = 60)
    {
        $company = Mage::app()->getStore()->getConfig(
            \Mageflow_Connect_Model_System_Config::API_COMPANY
        );
        $project = Mage::app()->getStore()->getConfig(
            \Mageflow_Connect_Model_System_Config::API_PROJECT
        );
        $data = array(
            'company'    => $company,
            'project'    => $project,
            'limit_days' => $limit
        );

        $client = Mage::helper('mageflow_connect/oauth')->getApiClient();
        $response = $client->get('changeset', $data);
        $changesetDataArr = json_decode($response, true);
        $changesetData = array();
        if (is_array($changesetDataArr)) {
            $changesetData = $changesetDataArr['items'];
        }
        $itemCollection = new Varien_Data_Collection();

        foreach ($changesetData as $changeset) {
            $data['id'] = $changeset['id'];
            $response = $client->get('changeset', $data);
            $changesetItemData = json_decode($response, true);
            foreach (
                $changesetItemData['items'][0]['items'] as $changesetItem
            ) {
                $itemCollection->addItem(
                    new Varien_Object(
                        array(
                            'id'         => $changesetItem['id'],
                            'changeset'  => $changeset['name'],
                            'type'       => $changesetItem['type'],
                            'created_at' => $changesetItem['created_at']
                        )
                    )
                );
            }
        }
        return $itemCollection;
    }

    /**
     * @return \Mageflow\Connect\Model\Api\Mageflow\Client
     */
    protected function getApiClient()
    {
        /**
         * @var Mageflow_Connect_Helper_Oauth $helper
         */
        $helper = Mage::helper('mageflow_connect/oauth');
        $client = $helper->getApiClient();
        return $client;
    }

    /**
     * @param $msg
     *
     * @return mixed
     */
    protected function log($msg)
    {
        $logHelper = Mage::helper('mageflow_connect/log');
        return $logHelper->log($msg);
    }

    /**
     * Returns last access time (frontend OR backend!)
     * of this Magento instance
     */
    public function getLastAccessTime()
    {
        /**
         * @var Mageflow_Connect_Model_Resource_System_Info_Performance_Collection $modelCollection
         */
        $modelCollection = Mage::getModel('mageflow_connect/system_info_performance')->getCollection();
        $modelCollection
            ->setOrder('created_at', 'DESC')
            ->setPageSize(1)
            ->setCurPage(1);

        /**
         * @var Mageflow_Connect_Model_System_Info_Performance $model
         */
        $model = $modelCollection->getFirstItem();

        //output current time if nothing can be found
        $out = time();
        if ($model instanceof Mageflow_Connect_Model_System_Info_Performance) {
            $createdAt = strtotime($model->getCreatedAt());
            $out = $createdAt;
        }
        return $out;
    }

    /**
     * get module version from config
     */
    public function getModuleVersion()
    {
        return Mage::getConfig()->getModuleConfig('Mageflow_Connect')->version;
    }

    /**
     * get module version from config
     */
    public function getModuleName()
    {
        return Mage::getConfig()->getModuleConfig('Mageflow_Connect')->name;
    }

}
