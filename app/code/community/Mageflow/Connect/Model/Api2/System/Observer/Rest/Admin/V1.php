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
 * Mageflow_Connect_Model_Api2_System_Observer_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_System_Observer_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'system_configuration';


    /**
     * Returns array with system info
     *
     * @return array
     */
    public function _retrieve()
    {
        $out = array();

        $list = Mage::app()->getConfig()->getXpath('/config//events');

        /**
         * @var Mage_Core_Model_Config_Element $event
         */
        foreach ($list as $event) {
            /**
             * @var Mage_Core_Model_Config_Element $element
             */
            foreach ($event->children() as $key => $element) {
                /**
                 * @var Mage_Core_Model_Config_Element $observer
                 */
                foreach ($element->children() as $observer) {
                    foreach ($observer->children() as $o) {
                        $data = (array)$o;
                        $out[]
                            = array(
                            'event' => $key,
                            'class' => $data['class'],
                            'method' => $data['method']
                        );
                    }
                }
            }
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