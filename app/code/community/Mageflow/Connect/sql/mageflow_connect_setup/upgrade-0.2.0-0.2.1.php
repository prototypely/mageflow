<?php

/**
 * upgrade-0.2.0-0.2.1.php
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
$installer = $this;

$installer->startSetup();

$table = $installer->getTable('core/config_data');
if (!$installer->getConnection()->tableColumnExists($table, 'created_at')) {
    $installer->getConnection()->addColumn(
        $table,
        'created_at',
        'DATETIME NOT NULL'
    );
}
if (!$installer->getConnection()->tableColumnExists($table, 'updated_at')) {
    $installer->getConnection()->addColumn(
        $table,
        'updated_at',
        'DATETIME NOT NULL'
    );
}

$indexFields = array('created_at', 'updated_at');
$indexName = $installer->getConnection()->getIndexName(
    $table,
    $indexFields
);
if (!in_array(
    $indexName,
    $installer->getConnection()->getIndexList($table)
)
) {
    $installer->getConnection()->addIndex($table, $indexName, $indexFields);
}
$installer->endSetup();