<?php

/**
 * Block.php
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
 * Mageflow_Connect_Model_Handler_Cms_Block
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_Cms_Block
    extends Mageflow_Connect_Model_Handler_Cms_Abstract
{
    /**
     * update or create cms/block from data array
     *
     * @param $data
     *
     * @return array
     */
    public function processData(array $data)
    {
        $data = isset($data[0]) ? $data[0] : $data;
        $message = null;
        $savedEntity = null;
        /**
         * @var Mage_Cms_Model_Block $model
         */
        $model = $this->findModel('cms/block', $data['mf_guid'], array('field' => 'identifier', 'value' => $data['identifier']));

        $data['block_id'] = $model->getBlockId();

        if (isset($data['stores'])) {
            $storeIdList = $this->getDataProcessor(get_class($model))->getStoreIdListByCodes($data['stores']);
            $data['stores'] = $storeIdList;
        } else {
            $storeEntity = Mage::getModel('core/store')
                ->load('default', 'code');
            $data['stores'][] = $storeEntity->getId();
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


}