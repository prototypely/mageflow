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
 * Mageflow_Connect_Model_Api2_System_Role_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_System_Role_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'system_role';


    /**
     * Returns array with system info
     *
     * @return array
     */
    public function _retrieve()
    {

        $out = array();
        $key = $this->getRequest()->getParam('key', null);
        if (null !== $key) {
            $modelCollection = $this->getWorkingModel()->getCollection();
            $modelCollection->addFieldToFilter(array('role_name', 'mf_guid'),
                array(
                    array('eq' => $key),
                    array('eq' => $key)
                )
            );
            $out = $this->packModelCollection($modelCollection);
        }
        return $out;
    }

    /**
     * retreive collection
     *
     * @return array
     */
    public function _retrieveCollection()
    {
        /**
         * @var Mage_Admin_Model_User $model
         */
        $model = $this->getWorkingModel();

        $itemCollection = $model->getCollection();

        return $this->packModelCollection($itemCollection);
    }

}