<?php

/**
 * PullController.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Controller
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_PullController
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Controller
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_PullController
    extends Mageflow_Connect_Controller_AbstractController
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('mageflow/connect');
        $this->_addContent(
            $this->getLayout()->createBlock(
                'mageflow_connect/adminhtml_pull',
                'mageflow_connect.pullgrid'
            )
        );

        $this->renderLayout();
    }

    /**
     * Pull changeset
     */
    public function pullAction()
    {
        $params = $this->getRequest()->getParams();
        $this->log($params);

        $company = Mage::app()->getStore()->getConfig(
            \Mageflow_Connect_Model_System_Config::API_COMPANY
        );
        $data = array(
            'company' => $company,
        );

        $client = $this->getApiClient();

        $idList = $this->getRequest()->getParam('id', array());
        $idArr = array();
        if (is_scalar($idList)) {
            $idArr[] = $idList;
        } else {
            $idArr = $idList;
        }
        $this->log($idArr);
        foreach ($idArr as $id) {
            $changesetItem = Mage::getModel('mageflow_connect/changeset_item_cache')
                ->load($id);
            $data['id'] = $changesetItem->getData('remote_id');
            $response = $client->get('changesetitem', $data);

            $itemArray = json_decode($response, true);
            $item = $itemArray['items'][0];
//            $this->log($item);
            $filteredData = json_decode($item['content'], true);
//            $this->log($filteredData);

            $typeName = str_replace('/', '_', $item['type']);

            $processingResponse = $this->getDataProcessor($typeName)->processData($filteredData);
//            $this->log($processingResponse);
            if ($processingResponse['status'] && $processingResponse['status'] == 'success') {
                $changesetItem->setStatus('Applied');
            } else {
                $changesetItem->setStatus('Failed');
            }
            $changesetItem->save();
        }
        $this->_redirect('*/pull/index');
    }
    /**
     * Pull changeset
     */
    public function applyAction()
    {
        $params = $this->getRequest()->getParams();
        $this->log($params);


        $idList = $this->getRequest()->getParam('id', array());
        $idArr = array();
        if (is_scalar($idList)) {
            $idArr[] = $idList;
        } else {
            $idArr = $idList;
        }
        $this->log($idArr);
        foreach ($idArr as $id) {
            /**
             * @var Mageflow_Connect_Model_Changeset_Item_Cache $changeSetItem
             */
            $changesetItem = Mage::getModel('mageflow_connect/changeset_item_cache')
                ->load($id);

            $typeName = str_replace('/', '_', $changesetItem->getType());

            $content = Mage::helper('core')->jsonDecode($changesetItem->getContent());
            try {
                $metaInfo = Zend_Json::decode($changesetItem->getData('meta_info'));
            } catch (\Exception $ex) {
                $metaInfo = [];
            }

            $processingResponse = $this->getDataProcessor($typeName)->processData($content, $metaInfo);
            if ($processingResponse['status'] && $processingResponse['status'] == 'success') {
                $changesetItem->setStatus('Applied');
            } else {
                $changesetItem->setStatus('Failed');
            }
            $changesetItem->save();
        }
        $this->_redirect('*/pull/index');
    }

    /**
     * Grid action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $contentBlock = $this->getLayout()->createBlock(
            'mageflow_connect/adminhtml_pull_grid'
        );
        $html = $contentBlock->toHtml();
        $this->getResponse()->setBody($html);
    }

    /**
     * Discards changesets
     */
    public function discardAction()
    {
        $idList = $this->getRequest()->getParam('id', array());
        $idArr = array();
        if (is_scalar($idList)) {
            $idArr[] = $idList;
        } else {
            $idArr = $idList;
        }
        foreach ($idArr as $id) {
            $changesetItem = Mage::getModel('mageflow_connect/changeset_item_cache')
                ->load($id);
            $changesetItem->delete();
        }
        $this->_redirect('*/pull/index');
    }

    /**
     * Refreshes changeset cache
     */
    public function refresChangeSetCacheAction()
    {
        $this->_expireAjax();
        /**
         * @var Mageflow_Connect_Model_Async_Itemcacheupdater $model
         */
        $model = Mage::getModel('mageflow_connect/async_itemcacheupdater');
        $model->run();

        $contentBlock = $this->getLayout()->createBlock(
            'mageflow_connect/adminhtml_pull_grid'
        );
        $html = $contentBlock->toHtml();
        return $this->getResponse()->setBody($html);
    }

    /**
     * Refreshes changeset cache
     */
    public function refreshPullGridPageAction()
    {
        /**
         * @var Mageflow_Connect_Model_Async_Itemcacheupdater $model
         */
        $model = Mage::getModel('mageflow_connect/async_itemcacheupdater');
        $model->run();

        $this->_redirect('*/pull/index');
    }

}
