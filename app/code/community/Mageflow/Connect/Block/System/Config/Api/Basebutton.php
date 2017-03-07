<?php

/**
 * Basebutton.php
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
 * Mageflow_Connect_Block_System_Config_Api_Basebutton
 *
 * BaseButton class that is used to generate buttons
 * in admin config section
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
abstract class Mageflow_Connect_Block_System_Config_Api_Basebutton
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * dummy
     *
     * @var
     */
    protected $_dummy;
    /**
     * field renderer
     *
     * @var
     */
    protected $_fieldRenderer;

    /**
     * render
     *
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $buttonBlock = $element->getForm()->getParent()->getLayout()
            ->createBlock('Mageflow_Connect_Block_Adminhtml_Widget_Button');
        $data = $this->getButtonData($buttonBlock);

        $id = $element->getHtmlId();

        $html = sprintf(
            '<tr><td class="label"><label for="%s">%s</label></td>',
            $id,
            $element->getLabel()
        );
        $html .= sprintf(
            '<td class="value">%s</td>',
            $buttonBlock->setData($data)->toHtml()
        );
        $html .= '</tr>';
        return $html;
    }

    /**
     * get button data
     *
     * @param $buttonBlock
     *
     * @return mixed
     */
    public abstract function getButtonData($buttonBlock);

    /**
     * get dummy element
     */
    protected function _getDummyElement()
    {
        if (empty($this->_dummy)) {
            $this->_dummy = new Varien_Object(
                array('show_in_default' => 1,
                    'show_in_website' => 0,
                    'show_in_store' => 0)
            );
        }
        return $this->_dummy;
    }

    /**
     * get field renderer
     */
    protected function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $this->_fieldRenderer = Mage::getBlockSingleton(
                'adminhtml/system_config_form_field'
            );
        }
        return $this->_fieldRenderer;
    }

}
