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
 * Mageflow_Connect_Model_Api2_System_User_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_System_User_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'system_user';


    /**
     * Returns array admin user entity as its single element.
     *
     * User can be searched by following parameters:
     * - mf_guid
     * - username
     * - email
     *
     * Examples:
     * /api/rest/system/user/14aa9ad136a21bca957b7bbed612c22706916566
     * /api/rest/system/user/admin
     * /api/rest/system/user/user@domain.com
     *
     * @return array
     */
    public function _retrieve()
    {

        $out = array();
        $key = $this->getRequest()->getParam('key', null);
        if (null !== $key) {
            $modelCollection = $this->getWorkingModel()->getCollection();
            $modelCollection->addFieldToFilter(array('username', 'email', 'mf_guid'),
                array(
                    array('eq' => $key),
                    array('eq' => $key),
                    array('eq' => $key)
                )
            );
            $out = $this->packModelCollection($modelCollection);
        }
        return $out;
    }

    /**
     * Returns list of admin users.
     *
     * @return array
     */
    public function _retrieveCollection()
    {
        return parent::_retrieveCollection();
    }

}