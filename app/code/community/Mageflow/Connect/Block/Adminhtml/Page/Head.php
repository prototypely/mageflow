<?php

/**
 * Head.php
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
 * Mageflow_Connect_Block_Adminhtml_Page_Head is a wrapper between
 * normal Mage_Page_Block_Html_Head. It's main task is to avoid errors
 * when we have no scripts to load (elsewhere than under MageFlow pages)crea
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_Adminhtml_Page_Head
    extends Mage_Page_Block_Html_Head
{
    /**
     * Overload of mage class to avoid errors when we
     * don't need to load any MageFlow scripts
     *
     * @return string
     */
    public function getCssJsHtml()
    {
        $section = $this->getAction()->getRequest()->getParam('section', false);
        $module = $this->getAction()->getRequest()->getControllerModule();
	    if ( stristr( $section, 'mageflow_connect' ) || stristr( $module, 'mageflow_connect' ) ) {
            return parent::getCssJsHtml();
        }
        return '';
    }
}