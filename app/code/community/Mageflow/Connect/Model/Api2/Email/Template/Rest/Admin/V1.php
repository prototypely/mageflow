<?php

/**
 * V1.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Model_Api2_Email_Template_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Email_Template_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'adminhtml/email_template';


    /**
     * retrieve
     *
     * @return array
     */
    public function _retrieve()
    {
        $key = $this->getRequest()->getParam('key');

        $out = array();
        /**
         * @var Mage_Core_Model_Resource_Email_Template_Collection $collection
         */
        $model = $this->getWorkingModel();
        /**
         * @var Varien_Data_Collection_Db $collection
         */
        $collection = $model->getCollection();
        $collection->addFilter('mf_guid', $key);
        $collection->addFilter('template_code', $key, 'OR');

        $model = $collection->getFirstItem();

        if ($model instanceof Mage_Core_Model_Email_Template) {
            $out[] = $this->packModel($model);
        }

        return $out;
    }

    /**
     * delete entities
     *
     * @param array $filteredData
     *
     * @return array
     */
    public function _multiDelete(array $filteredData)
    {
        $this->log($filteredData);

        $templateEntity = Mage::getModel('adminhtml/email_template')
            ->load($filteredData['mf_guid'], 'mf_guid');

        $originalData = $templateEntity->getData();
        $rollbackFeedback = array();
        // send overwritten data to mageflow
        if ($originalData) {
            $rollbackFeedback = $this->sendRollback(
                str_replace('_', ':', $this->_resourceType),
                $filteredData,
                $originalData
            );
        } else {
            $this->sendJsonResponse(
                array('notice' => 'target not found or empty, mf_guid='
                    . $filteredData['mf_guid'])
            );
        }
        try {
            $templateEntity->delete();
            $this->sendJsonResponse(
                array_merge(
                    array('message' =>
                        'target deleted, mf_guid=' . $filteredData['mf_guid']),
                    $rollbackFeedback
                )
            );
        } catch (Exception $e) {
            $this->sendJsonResponse(
                array_merge(
                    array('delete error' => $e->getMessage()),
                    $rollbackFeedback
                )
            );
        }
    }
}
