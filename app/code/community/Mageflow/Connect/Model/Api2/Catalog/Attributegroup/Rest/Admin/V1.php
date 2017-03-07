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
 * Mageflow_Connect_Model_Api2_Catalog_Attributegroup_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Catalog_Attributegroup_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'catalog_attributegroup';

    /**
     * Creation of catalog/attributegroup is currently not supported
     *
     * @throws Exception
     * @throws Mage_Api2_Exception
     */

    public function _multiCreate()
    {
        return $this->_critical('Not implemented', 501);
    }

    /**
     * retrieve
     *
     * @return array
     */
    public function _retrieve()
    {
        $dataProcessor = $this->getDataProcessor();
        $entityTypeId = $this->getRequest()->getParam('entity_type_id', null);
        $name = $this->getRequest()->getParam('attribute_group_name', null);
        $attributeSetGuid = $this->getRequest()->getParam('attribute_set_id', null);
        $attributeSetModel = Mage::getModel('eav/entity_attribute_set')->load($attributeSetGuid, 'mf_guid');

        /**
         * @var Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection $collection
         */
        $collection = $dataProcessor->getEntityCollection(
            array(
                'entity_type_id'       => array('eq' => $entityTypeId),
                'attribute_group_name' => array('eq' => $name),
                'attribute_set_id'     => array('eq' => $attributeSetModel->getId(), 'alias' => 'table_alias')
            )
        );

        $out = $this->packModelCollection($collection);

        return $out;
    }

    /**
     * retrieve collection
     *
     * @return array
     */
    public function _retrieveCollection()
    {
        $dataProcessor = $this->getDataProcessor();

        $collection = $dataProcessor->getEntityCollection();

        $out = $this->packModelCollection($collection);

        return $out;
    }

    /**
     * multidelete
     *
     * @param array $filteredData
     */
    public function _multiDelete(array $filteredData)
    {
        $this->log(sprintf('%s', $filteredData));
        $this->_error('Not implemented yet', 500);
        return;

    }

}
