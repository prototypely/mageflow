<?php

/**
 * upgrade-1.2.0-1.2.1.php
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

$isDevMode = Mage::getIsDeveloperMode();
Mage::setIsDeveloperMode(true);

/* @var $installer Mageflow_Connect_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$tableName = 'mageflow_connect/changeset_item_cache';

$table = $installer->getTable($tableName);
if ($table && !$installer->getConnection()->isTableExists($table)) {
    $tableDef = $installer->getConnection()->newTable($table)
        ->addColumn(
            'id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
            ),
            'Record ID'
        )
        ->addColumn(
            'remote_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'nullable' => false,
                'unsigned' => true
            ),
            'Remote changeset item ID'
        )
        ->addColumn(
            'type',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable' => false,
                'length' => 255,
            ),
            'Changeset Item Type'
        )
        ->addColumn(
            'description',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable' => false,
                'length' => 255,
            ),
            'Changeset description'
        )
        ->addColumn(
            'meta_info',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable' => false,
                'length' => 255,
            ),
            'Changeset item metainfo'
        )
        ->addColumn(
            'content',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            array(
                'nullable' => false
            ),
            'Changeset item contents'
        )
        ->addColumn(
            'created_by',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable' => true
            ),
            'User who created changeset item '
        )
        ->addColumn(
            'created_at',
            Varien_Db_Ddl_Table::TYPE_DATETIME,
            null,
            array(
                'nullable' => false
            ),
            'Changeset item creation time'
        )
        ->addColumn(
            'updated_at',
            Varien_Db_Ddl_Table::TYPE_DATETIME,
            null,
            array(
                'nullable' => false,
            ),
            'Changeset item modification time'
        )
        ->addColumn(
            'mf_guid',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            64,
            array(
                'nullable' => false,
                'length' => 64
            ),
            'Changeset item GUID at REMOTE SYSTEM'
        )
        ->addIndex('ix_remote_id', array('remote_id'))
        ->addIndex('ix_mf_guid', array('mf_guid'))
        ->addIndex('ix_updated_at', array('updated_at'))
        ->addIndex('ix_created_by', array('created_by'))
        ->addIndex('ix_created_at', array('created_at'))
        ->addIndex('ix_description', array('description'))
        ->addIndex('ix_type', array('type'));
    $installer->getConnection()->createTable($tableDef);
}
$installer->endSetup();

Mage::setIsDeveloperMode($isDevMode);