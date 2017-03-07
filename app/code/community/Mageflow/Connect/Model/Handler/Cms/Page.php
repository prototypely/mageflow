<?php

/**
 * Page.php
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
 * Mageflow_Connect_Model_Handler_Cms_Page
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_Cms_Page
    extends Mageflow_Connect_Model_Handler_Cms_Abstract
{
    /**
     * update or create cms/page from data array
     *
     * @param array $data
     *
     * @return array
     * @throws Exception
     */
    public function processData(array $data)
    {
        $data = isset($data[0]) ? $data[0] : $data;
        $savedEntity = null;
        $message = null;

        if (isset($data['stores']) && is_array($data['stores']) && count($data['stores'])) {
            $storeIdList = $this->getStoreIdListByCodes($data['stores']);
            if ($storeIdList == array()) {
                throw new Exception('no matching stores');
            }
            if (count($data['stores']) != count($storeIdList)) {
                $message =
                    "Notice: following store views are missing from target: "
                    . $this->getMissingStores($data['stores']);
            }
            $data['stores'] = $storeIdList;
        } else {
            $data['stores'] = array();
        }

        /**
         * @var Mage_Cms_Model_Page $model
         */
        $model = $this->findModel('cms/page', $data['mf_guid'], array('field' => 'identifier', 'value' => $data['identifier'], 'stores' => $data['stores']));
        $data['page_id'] = $model->getPageId();


        try {
            $savedEntity = $this->saveItem($model, $data);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $this->log($ex->getMessage());
            $this->log($ex->getTraceAsString());
        }

        return $this->sendProcessingResponse($savedEntity, $message);
    }
}