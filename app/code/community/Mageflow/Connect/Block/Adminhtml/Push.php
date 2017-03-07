<?php

/**
 * Push.php
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
 * Mageflow_Connect_Block_Adminhtml_Push
 * This class is used to display migration grid
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_Adminhtml_Push
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->removeButton('add');
        $this->_controller = 'adminhtml_push';
        $this->_blockGroup = 'mageflow_connect';
        $this->_headerText = Mage::helper('mageflow_connect')->__('Local Change Items');

        /**
         * @var Mageflow_Connect_Helper_Type $typeHelper
         */
        $typeHelper = Mage::helper('mageflow_connect/type');

        if ($typeHelper->isTypeEnabled('media_file')) {
            $this->_addButton(
                'refresh',
                array(
                    'label' => 'Refresh Media Index',
                    'onclick' => 'new Ajax.Request(\'' .
                        $this->getUrl('*/*/refreshmediaindex') . '\')',
                    'class' => '',
                    'before_html' => $this->getBeforeHtml()
                )
            );
        }

        $this->addButton('refresh', array(
            'label' => 'n/a',
            'before_html' => $this->getBeforeHtml(),
            'onclick' => "window.location = '" . $this->getUrl('*/*/refreshpullgridpage') . "';",
            'class'=>'no-display'
        ));
    }

    /**
     * Returns html before Push button
     * @return string
     */
    protected function getBeforeHtml()
    {
        $groundRulesUrl = Mage::app()->getStore()->getConfig(Mageflow_Connect_Model_System_Config::GROUND_RULES_URL);
        $html = sprintf(
            '<strong><a href="%s" id="MFGroundRules" target="_blank">%s</a></strong> ',
            $groundRulesUrl,
            Mage::helper('mageflow_connect')->__('NB! Please read the Ground Rules before migration!')
        );
        return $html;
    }
}
