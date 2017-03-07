<?php

/**
 * Widget.php
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
 * Mageflow_Connect_Model_Handler_Cms_Widget
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com)
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_Cms_Widget extends Mageflow_Connect_Model_Handler_Abstract
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

        $modelByIdentifier = Mage::getModel('widget/widget_instance')
            ->load($data['title'], 'title');

        $modelByMfGuid = Mage::getModel('widget/widget_instance')
            ->load($data['mf_guid'], 'mf_guid');

        if ($modelByIdentifier->getInstanceId()) {
            $model = $modelByIdentifier;
        }
        if ($modelByMfGuid->getInstanceId()) {
            $model = $modelByMfGuid;
        }

        if (null === $model) {
            $model = Mage::getModel('widget/widget_instance');
        }

        if ($model->getInstanceId()) {
            $data['instance_id'] = $model->getInstanceId();
        }

        if (!isset($data['widget_parameters'])) {
            throw new Exception('No widget parameters');
        }

        if (isset($data['widget_parameters']['page'])) {
            $pageEntity = null;
            $pageEntity = Mage::getModel('cms/page')->load($data['widget_parameters']['page'], 'identifier');
            if (is_null($pageEntity)) {
                throw new Exception('Target Page not found');
            }
            $data['widget_parameters']['page_id'] = $pageEntity->getPageId();
            unset($data['widget_parameters']['page']);
        }

        if (isset($data['widget_parameters']['block'])) {
            $blockEntity = null;
            $blockEntity = Mage::getModel('cms/block')->load($data['widget_parameters']['block'], 'identifier');
            $data['widget_parameters']['block_id'] = $blockEntity->getBlockId();
            if (is_null($blockEntity)) {
                throw new Exception('Target Block not found');
            }
            unset($data['widget_parameters']['block']);
        }

        if (isset($data['widget_parameters']['category'])) {
            $categoryEntity = null;
            $categoryEntity = Mage::getModel('Mage_Catalog_Model_Category')
                ->getCollection()->addFieldToFilter(array(
                        array('attribute'=>'mf_guid', 'eq' => $data['widget_parameters']['category'])
                    ))
                ->getFirstItem();
            $data['widget_parameters']['id_path'] = 'category/' . $categoryEntity->getEntityId();
            if (is_null($categoryEntity)) {
                throw new Exception('Target Category not found');
            }
            unset($data['widget_parameters']['category']);
        }

        if (isset($data['widget_parameters']['product'])) {
            $productEntity = null;
            $productEntity = Mage::getModel('Mage_Catalog_Model_Product')
                ->getCollection()->addFieldToFilter(array(
                        array('attribute'=>'mf_guid', 'eq' => $data['widget_parameters']['product'])
                    ))
                ->getFirstItem();
            $data['widget_parameters']['id_path'] = 'product/' . $productEntity->getEntityId();
            if (is_null($productEntity)) {
                throw new Exception('Target Procduct not found');
            }
            unset($data['widget_parameters']['product']);
        }

        if (isset($data['stores'])) {
            $data['store_ids'] = implode(',', $this->getStoreIdListByCodes($data['stores']));
            unset($data['stores']);
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
        $c->stores = array();
        foreach ($this->findStoresByIds(explode(',', $model->getData('store_ids'))) as $storeEntity) {
            $c->stores[] = $storeEntity->getCode();
        }
        $widgetParameters = $model->getWidgetParameters();

        if (isset($widgetParameters['page_id'])) {
            $pageEntity = Mage::getModel('cms/page')->load($widgetParameters['page_id']);
            $widgetParameters['page'] = $pageEntity->getIdentifier();
            unset($widgetParameters['page_id']);
        }

        if (isset($widgetParameters['block_id'])) {
            $blockEntity = Mage::getModel('cms/block')->load($widgetParameters['block_id']);
            $widgetParameters['block'] = $blockEntity->getIdentifier();
            unset($widgetParameters['block_id']);
        }

        if (isset($widgetParameters['id_path'])) {
            $toArray = explode('/', $widgetParameters['id_path']);
            $targetType = $toArray[0];
            $targetId = $toArray[1];

            if ($targetType == 'category') {
                $categoryEntity = Mage::getModel('Mage_Catalog_Model_Category')->load($targetId);
                $widgetParameters['category'] = $categoryEntity->getMfGuid();
            }

            if ($targetType == 'product') {
                $categoryEntity = Mage::getModel('Mage_Catalog_Model_Product')->load($targetId);
                $widgetParameters['product'] = $categoryEntity->getMfGuid();
            }

            unset($widgetParameters['id_path']);
        }

        $c->widget_parameters = $widgetParameters;
        return $c;
    }

    public function getPreview(Mageflow_Connect_Model_Interfaces_Changeitem $item)
    {
        $out = '';

        $object = json_decode($item->getContent());
        if ($object->title) {
            $out = $object->title;
        }
        return $out;
    }
} 