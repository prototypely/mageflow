<?php

/**
 * Grid.php
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
 * Mageflow_Connect_Block_Adminhtml_Pullgrid_Grid
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Block
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Block_Adminhtml_Pull_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * items
     *
     * @var false|Mage_Core_Model_Abstract
     */
    private $_items;


    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('pullGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Returns item collection
     *
     * @return Varien_Data_Collection
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * prepare collection
     *
     * @return Mageflow_Connect_Block_Adminhtml_Pull_Grid
     */
    protected function _prepareCollection()
    {
        /**
         * @var Varien_Data_Collection $collection
         */
        $collection = Mage::getModel('mageflow_connect/changeset_item_cache')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns
     *
     * @return Mageflow_Connect_Block_Adminhtml_Pull_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            array(
                'header' => Mage::helper('mageflow_connect')->__('ID'),
                'width' => '50px',
                'index' => 'remote_id',
                'type' => 'text',
            )
        );
        $this->addColumn(
            'type',
            array(
                'header' => Mage::helper('mageflow_connect')->__('Type'),
                'index' => 'type',
                'type' => 'text',
            )
        );
        $this->addColumn(
            'description',
            array(
                'header' => Mage::helper('mageflow_connect')->__('Description'),
                'index' => 'description',
                'type' => 'text'
            )
        );
        $this->addColumn(
            'preview',
            array(
                'header' => Mage::helper('mageflow_connect')->__('Preview'),
                'index' => 'preview',
                'renderer' => 'Mageflow_Connect_Block_Adminhtml_Push_Grid_Column_Renderer',
                'filter' => false,
                'sortable' => false
            )
        );
        $this->addColumn(
            'mf_guid',
            array(
                'header' => Mage::helper('mageflow_connect')->__('MF GUID'),
                'index' => 'mf_guid',
                'renderer' => 'Mageflow_Connect_Block_Adminhtml_Push_Grid_Column_Renderer_Mfguid',
                'filter' => false
            )
        );
        $this->addColumn(
            'status',
            array(
                'header' => Mage::helper('mageflow_connect')->__('Status'),
                'index' => 'status'
            )
        );
        $this->addColumn(
            'created_by',
            array(
                'header' => Mage::helper('mageflow_connect')->__('Created by'),
                'index' => 'created_by',
                'type' => 'text',
            )
        );
        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('mageflow_connect')->__('Created at'),
                'index' => 'created_at',
                'type' => 'text',
            )
        );
        $this->addColumn(
            'action',
            array(
                'header' => Mage::helper('mageflow_connect')->__('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
//                    array(
//                        'caption' => Mage::helper('mageflow_connect')->__(
//                            'Pull'
//                        ),
//                        'url' => array('base' => '*/*/pull'),
//                        'field' => 'id'
//                    ),
                    array(
                        'caption' => Mage::helper('mageflow_connect')->__(
                            'Apply'
                        ),
                        'url' => array('base' => '*/*/apply'),
                        'field' => 'id'
                    ),
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'id',
                'is_system' => true,
            )
        );

        $this->addExportType(
            '*/*/exportCsv',
            Mage::helper('customer')->__('CSV')
        );
        $this->addExportType(
            '*/*/exportXml',
            Mage::helper('customer')->__('Excel XML')
        );
        return parent::_prepareColumns();
    }

    /**
     * Returns row url
     *
     * @param Varien_Object $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        $url = $this->getUrl('*/*/*');
        return $url;
    }

    /**
     * get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * prepare massaction
     *
     * @return Mageflow_Connect_Block_Adminhtml_Pull_Grid
     */
    public function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem(
            'pull',
            array(
                'label' => Mage::helper('mageflow_connect')->__(
                    'Apply changes'
                ),
                'url' => $this->getUrl('*/*/apply'),
                'confirm' => Mage::helper('mageflow_connect')->__(
                    'Are you sure you want to apply these changes?'
                )
            )
        );
        $this->getMassactionBlock()->addItem(
            'discard',
            array(
                'label' => Mage::helper('mageflow_connect')->__(
                    'Discard change items'
                ),
                'url' => $this->getUrl('*/*/discard'),
                'confirm' => Mage::helper('mageflow_connect')->__(
                    'Are you sure you want to discard these changes?'
                )
            )
        );
        $this->_exportTypes = array();
        return $this;
    }

}
