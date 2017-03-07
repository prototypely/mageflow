<?php

/**
 * upgrade-0.9.1-0.9.2.php
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

$tableName = 'mageflow_connect/media_index';

if (!$installer->getConnection()->isTableExists(
    $installer->getTable($tableName)
)
) {
    $table = $installer->getConnection()->newTable($installer->getTable($tableName))
        ->addColumn(
            'media_index_id',
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
            'filename',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable' => false,
                'length' => 255,
            ),
            'File full path'
        )
        ->addColumn(
            'basename',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable' => false,
                'length' => 255,
            ),
            'File base name'
        )
        ->addColumn(
            'path',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable' => false,
                'length' => 255,
            ),
            'File absolute path'
        )
        ->addColumn(
            'mtime',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'nullable' => false
            ),
            'File mtime'
        )
        ->addColumn(
            'hash',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            64,
            array(
                'nullable' => false,
                'length' => 64,
            ),
            'File name hash'
        )
        ->addColumn(
            'name',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable' => false,
                'length' => 255,
            ),
            'Name'
        )
        ->addColumn(
            'short_name',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable' => false,
                'length' => 255,
            ),
            'Short file name'
        )
        ->addColumn(
            'url',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable' => false
            ),
            'Absolute file URL'
        )
        ->addColumn(
            'width',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'nullable' => false,
            ),
            'Image width'
        )
        ->addColumn(
            'height',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'nullable' => false,
            ),
            'Image height'
        )
        ->addColumn(
            'thumb_url',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable' => false,
                'length' => 255
            ),
            'Thumbnail URL'
        )
        ->addColumn(
            'type',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            16,
            array(
                'nullable' => true,
                'length' => 16
            ),
            'File MIME type'
        )
        ->addColumn(
            'size',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'nullable' => false,
            ),
            'File size'
        )
        ->addColumn(
            'created_at',
            Varien_Db_Ddl_Table::TYPE_DATETIME,
            null,
            array(
                'nullable' => false
            ),
            'File creation time'
        )
        ->addColumn(
            'updated_at',
            Varien_Db_Ddl_Table::TYPE_DATETIME,
            null,
            array(
                'nullable' => false
            ),
            'File modification time'
        )
        ->addColumn(
            'mf_guid',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            64,
            array(
                'nullable' => false,
                'length' => 64
            ),
            'MF GUID of index item'
        )
        ->addIndex('ix_path', array('path'))
        ->addIndex('ix_mf_guid', array('mf_guid'))
        ->addIndex('ix_updated_at', array('updated_at'))
        ->addIndex('ix_created_at', array('created_at'))
        ->addIndex('ix_name', array('name'))
        ->addIndex('ix_basename', array('basename'))
        ->addIndex('ix_size', array('size'))
        ->addIndex('ix_hash', array('hash'))
        ->addIndex('ix_mtime', array('mtime'))
        ->addIndex('ix_type', array('type'));
    $installer->getConnection()->createTable($table);
}
$tableName = 'mageflow_changeset_item';
if (
$installer->getConnection()->isTableExists($installer->getTable($tableName))
) {
    $installer->getConnection()->modifyColumn(
        $installer->getTable($tableName),
        'content',
        'LONGTEXT'
    );
}
$installer->endSetup();

Mage::setIsDeveloperMode(true);