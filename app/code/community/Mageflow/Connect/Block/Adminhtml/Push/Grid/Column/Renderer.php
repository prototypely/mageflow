<?php

/**
 * Renderer.php
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
 * Mageflow_Connect_Block_Adminhtml_Migrate_Grid_Column_Renderer
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_Adminhtml_Push_Grid_Column_Renderer
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * render
     *
     * @param Varien_Object $row
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $output = 'Preview N/A';
        if ($row->getType()) {
            $typeName = str_replace(array(':', '/'), '_', $row->getType());
            $handlerClass = $this->getTypeHelper()->getHandlerClass($typeName);
            if ($handlerClass != '') {
                /**
                 * @var Mageflow_Connect_Model_Handler_Abstract $handler
                 */
                $handler = Mage::getModel($handlerClass);
                $output = $handler->getPreview($row);
            }
        }
        if (strlen($output) > 50) {
            $output = substr($output, 0, 50) . '...';
        }
        return $output;
    }

    /**
     * @return Mageflow_Connect_Helper_Type
     */
    protected function getTypeHelper()
    {
        return Mage::helper('mageflow_connect/type');
    }
}
