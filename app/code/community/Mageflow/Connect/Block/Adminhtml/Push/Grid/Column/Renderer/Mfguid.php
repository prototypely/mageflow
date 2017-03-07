<?php

/**
 * Mfguid.php
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
 * Mageflow_Connect_Block_Adminhtml_Migrate_Grid_Column_Renderer_Mfguid
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_Adminhtml_Push_Grid_Column_Renderer_Mfguid
    extends Mageflow_Connect_Block_Adminhtml_Push_Grid_Column_Renderer
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
        $output = 'no mf guid';
        if ($row->getType()) {
            $typeName = str_replace(':', '_', $row->getType());
            $typeName = str_replace('/', '_', $typeName);
            $handlerClass = $this->getTypeHelper()->getHandlerClass($typeName);
            if ($handlerClass != '') {
                /**
                 * @var Mageflow_Connect_Model_Handler_Abstract $handler
                 */
                $handler = Mage::getModel($handlerClass);
                $output = substr($handler->getMfGuid($row), 0, 6);
            }
        }

        return $output;
    }

}
