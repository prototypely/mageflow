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
 * Mageflow_Connect_Model_Api2_Newsletter_Template_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Newsletter_Template_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'newsletter/template';


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
         * @var Mage_Core_Model_Resource_Newsletter_Template_Collection $collection
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
     * retrieve collection
     *
     * @return array
     */
    public function _retrieveCollection()
    {
        return $this->_retrieve();
    }
}
