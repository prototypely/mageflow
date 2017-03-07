<?php

/**
 * Pull.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Block_Adminhtml_Pull
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_Adminhtml_Pull
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->removeButton('add');
        /**
         * @var Mageflow_Connect_Model_Changeset_Item_Cache $model
         */
        $this->_controller = 'adminhtml_pull';
        $this->_blockGroup = 'mageflow_connect';
        $this->_headerText = Mage::helper('mageflow_connect')->__(
            'Change Item Inbox'
        );

    }

    /**
     * Returns html before refresh button
     *
     * @param $model
     * @return string
     */
    protected function getBeforeHtml($model)
    {
        $groundRulesUrl = Mage::app()->getStore()->getConfig(Mageflow_Connect_Model_System_Config::GROUND_RULES_URL);
        $html = sprintf(
            '<strong><a href="%s" id="MFGroundRules" target="_blank">%s</a></strong> ',
            $groundRulesUrl,
            Mage::helper('mageflow_connect')->__('NB! Please read the Ground Rules before migration!')
        );
        $html .= 'Index updated at ' . $model->getLastUpdated()->toString(Zend_Date::DATETIME_MEDIUM);
        return $html;
    }
}
