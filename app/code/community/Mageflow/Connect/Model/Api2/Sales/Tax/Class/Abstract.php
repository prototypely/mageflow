<?php

/**
 * Abstract.php
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
 * Mageflow_Connect_Model_Api2_Sales_Tax_Class_Abstract
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Sales_Tax_Class_Abstract
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType;

    /**
     * class type
     *
     * @var string
     */
    protected $_classType;

    /**
     * retrieve
     *
     * @return array
     */
    public function _retrieve()
    {
        $mfGuid = $this->getRequest()->getParam('mf_guid', null);

        $out = array();

        $resourceModel = $this->getWorkingModel();
        $itemCollection = $resourceModel->getCollection();
        $itemCollection->addFieldToFilter(
            'class_type', array('eq' => $this->_classType)
        );

        if (!is_null($mfGuid)) {
            $itemCollection->addFieldToFilter(
                'mf_guid', array('eq' => $mfGuid)
            );
        }

        $out = $this->packModelCollection($itemCollection);

        return $out;
    }

    /**
     * retrieve collection
     *
     * @return array
     */
    public function _retrieveCollection()
    {
        return $this->_retrieve();
    }}
