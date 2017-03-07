<?php

/**
 * Tabs.php
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
 * Mageflow_Connect_Block_Adminhtml_Push_Tabs
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_Adminhtml_Push_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct();
        $this->setId('push_tabs');
    }

    /**
     * function to return correct version of this array
     * to be extended in different mfx versions
     * 
     * @return type
     */
    protected function getTypeMap() {
        return Mage::getModel('mageflow_connect/system_config')->getTypeMap();
    }
    
    /**
     * create tabs by the list of tabs
     * the official list shall be reviewed
     * 
     * @return type
     */
    protected function _prepareLayout() {

        $typeMap = $this->getTypeMap();

        foreach ($typeMap as $key => $type) {
            if (Mage::app()->getStore()->getConfig('mageflow_connect/enabled_types/' . $type['config'])) {
                $this->addTab($key, array(
                    'label' => $type['label'],
                    'url' => $this->getUrl('*/*/' . $key, array('_current' => true))
                    //'class' => 'ajax'
                ));
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * Translate html content
     *
     * @param string $html
     * @return string
     */
    protected function _translateHtml($html) {
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        return $html;
    }

}
