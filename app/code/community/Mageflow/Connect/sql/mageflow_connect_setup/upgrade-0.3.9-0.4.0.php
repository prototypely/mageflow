<?php

/**
 * upgrade-0.3.9-0.4.0.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Sql Install & Upgrade
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/* @var $installer Mageflow_Connect_Model_Resource_Setup */
/**
 * This update script adds special "unique id" column to each entity
 * that is manageable by MageFlow. Currently these entities are:
 * - cms block
 * - cms page
 * - configuration item
 * - catalog category
 * - backend user
 * - oauth consumers
 * - product attribute
 * - product attribute set
 * - product attribute group
 */

$isDevMode = Mage::getIsDeveloperMode();
Mage::setIsDeveloperMode(true);

$installer = $this;

$installer->startSetup();

/**
 * @var Mageflow_Connect_Helper_Type $typeHelper
 */
$typeHelper = Mage::helper('mageflow_connect/type');
$typesToBeChecked = $typeHelper->getTypes();

$guidColumn = 'mf_guid';
$updatedAtColumn = 'updated_at';
$createdAtColumn = 'created_at';

foreach ($typesToBeChecked as $type) {
    if ($type->short != '') {
        //check for table because we have some non-table types, too
        $table = null;
        try {
            if (null !== $type->table) {
                $table = $installer->getTable($type->table);
            }
            if (null == $table) {
                $table = $installer->getTable($type->short);
            }
        } catch (Exception $ex) {
        }
        if (null !== $table && $installer->getConnection()->isTableExists($table)) {
            //add GUID column
            if (!$installer->getConnection()
                ->tableColumnExists($table, $guidColumn)
            ) {
                $installer->getConnection()->addColumn(
                    $table,
                    $guidColumn,
                    'VARCHAR(64) NULL'
                );
            }
            $keyNameList = array();
            foreach ($installer->getConnection()->getIndexList($table) as $index) {
                $keyNameList[] = $index['KEY_NAME'];
            }
            $indexName = 'ix_' . $guidColumn;
            if (!in_array($indexName, array_values($keyNameList))) {
                $installer->getConnection()->addIndex(
                    $table,
                    'ix_' . $guidColumn,
                    array($guidColumn),
                    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
                );
            }
            //add created_at column
            if (!$installer->getConnection()
                ->tableColumnExists($table, $createdAtColumn)
            ) {
                $installer->getConnection()->addColumn(
                    $table,
                    $createdAtColumn,
                    'DATETIME NULL'
                );
            }
            //add updated_at column
            if (!$installer->getConnection()->tableColumnExists(
                $table,
                $updatedAtColumn
            )
            ) {
                $installer->getConnection()->addColumn(
                    $table,
                    $updatedAtColumn,
                    'DATETIME NULL'
                );
            }
        }
    }
}
$entitySetup = new Mage_Eav_Model_Entity_Setup('core_setup');
$entitySetup->startSetup();
if (!$entitySetup->getAttribute('catalog_product', 'mf_guid')) {
    $entitySetup->addAttribute(
        'catalog_product',
        'mf_guid',
        array(
            'type' => 'static',
            'group' => 'General',
            'visible' => false,
            'required' => false,
            'backend' => 'mageflow_connect/types_mfguid',
            'label' => 'MF GUID',
            'input' => 'hidden'
        )
    );

}
if (!$entitySetup->getAttribute('catalog_category', 'mf_guid')) {
    $entitySetup->addAttribute(
        'catalog_category',
        'mf_guid',
        array(
            'type' => 'static',
            'label' => 'MF GUID',
            'backend' => 'mageflow_connect/types_mfguid',
            'visible' => false,
            'required' => false,
            'input' => 'hidden',
            'group' => 'General Information',
        )
    );
}
$entitySetup->endSetup();

$installer->endSetup();

Mage::setIsDeveloperMode($isDevMode);