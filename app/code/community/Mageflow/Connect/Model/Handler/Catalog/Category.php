<?php

/**
 * Category.php
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
 * Mageflow_Connect_Model_Handler_Catalog_Categpry
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_Catalog_Category
    extends Mageflow_Connect_Model_Handler_Abstract
{
    /**
     * update or create catalog/category from data array
     *
     * @param $data
     *
     * @return array|null
     */
    public function processData(array $data)
    {
        $model = null;

        $data = isset($data[0]) ? $data[0] : $data;
        /**
         * @var Mage_Core_Model_Resource_Db_Collection_Abstract
         */
        $modelCollection = Mage::getModel('catalog/category')
            ->getCollection()
            ->addFieldToFilter('mf_guid', $data['mf_guid']);

        $model = $modelCollection->getFirstItem();

        if (!($model instanceof Mage_Catalog_Model_Category) || $model->getId() < 1) {
            $model = Mage::getModel('catalog/category');
        }

        if ($model->getData('entity_id')) {
            $data['entity_id'] = $model->getData('entity_id');
        }


        $rootCategory = Mage::getModel('catalog/category')
            ->getCollection()
            ->addFieldToFilter('parent_id', 0)
            ->load()
            ->getFirstItem();

        $parentCategory = Mage::getModel('catalog/category')
            ->getCollection()
            ->addFieldToFilter('mf_guid', $data['parent_id'])
            ->load()
            ->getFirstItem();

        $this->log('Root Category ID: ' . $rootCategory->getId());

        $this->log('Parent Category ID: ' . $parentCategory->getId());

        unset($data['path']);
        unset($data['parent_id']);


        if ($parentCategory->getEntityId() == 0) {
            $this->log('parent was not found');
            $this->log($data['parent_id']);

            $parentId = (int)$rootCategory->getEntityId();

            $this->log('replacing parent');
            $this->log($data['parent_id']);

        } else {
            $parentId = (int)$parentCategory->getEntityId();
        }
        $mfGuid = $data['mf_guid'];

        $this->log($data);

        $data['parent_id'] = $parentId;

        $message = null;
        $savedEntity = null;

        try {
            $model->setMfGuid($mfGuid);
            $savedEntity = $this->saveItem($model, $data);
            //move only if not root category
            if ($savedEntity->getId() != $rootCategory->getId()) {
                $savedEntity->move($parentId, $parentId);
            }
        } catch (Exception $ex) {
            $savedEntity = null;
            $message = $ex->getMessage();
            $this->log($ex->getMessage());
            $this->log($ex->getTraceAsString());
        }

        return $this->sendProcessingResponse($savedEntity, $message);
    }

    /**
     * pack content
     *
     * @param $model
     *
     * @return array
     */
    public function packData(Mage_Core_Model_Abstract $model)
    {

        //reload fresh model from DB
        $modelCollection = Mage::getModel('catalog/category')
            ->getCollection()
            ->addFieldToFilter('entity_id', $model->getId());
        $model = $modelCollection->getFirstItem();

        $model->load($model->getId());

        $c = $this->packModel($model);

        if (null !== $model->getPath()) {
            $pathIdList = explode('/', $model->getPath());
            $fixedPath = array();
            foreach ($pathIdList as $pathId) {
                $categoryInPath = Mage::getModel('catalog/category')
                    ->load($pathId);
                $fixedPath[] = $categoryInPath->getMfGuid();
            }
            $c->path = implode('/', $fixedPath);
        }


        if ('' != trim($model->getParentId())) {
            $parentCategory = $model->getParentCategory();
            $c->parent_id = $parentCategory->getMfGuid();
        } else {
            //experimentally return more REST-like response with less DATA (content) and more INFO
            //TODO review and create better logic for different responses
            $c = new stdClass();
            $c->entity_id = $model->getId();
            $c->mf_guid = $model->getMfGuid();
            $c->get = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'api/rest/catalog/category/' . $c->mf_guid;
            $c->put = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'api/rest/catalog/category/' . $c->mf_guid;
            $c->post = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'api/rest/catalog/category';
        }
        return $c;
    }

    /**
     * @param Mageflow_Connect_Model_Interfaces_Changeitem $row
     * @return string|void
     */
    public function getPreview(Mageflow_Connect_Model_Interfaces_Changeitem $row)
    {
        $content = json_decode($row->getContent());
        $output = '';
        if ($content->name) {
            $output = $content->name;
        }
        return $output;
    }

}