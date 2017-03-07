<?php

/**
 * upgrade-1.2.8-1.3.0.php
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

/* @var Mageflow_Connect_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup();

$tableName = 'mageflow_connect/changeset_item';

$table = $installer->getTable($tableName);

$connection = $installer->getConnection();
$columnList = array(
    'mf_guid' => array(
        'def' => 'VARCHAR(64)',
        'index' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    'checksum' => array(
        'def' => 'VARCHAR(40)',
        'index' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    ),
);
if ($table && $connection->isTableExists($table)) {
    foreach ($columnList as $columnName => $columnDef)
        if (!$connection->tableColumnExists($table, $columnName)) {
            $connection->addColumn(
                $table,
                $columnName,
                $columnDef['def']
            );
        }

    $keyNameList = array();
    foreach ($installer->getConnection()->getIndexList($table) as $index) {
        $keyNameList[] = $index['KEY_NAME'];
    }
    $indexName = 'ix_' . $columnName;
    if (!in_array($indexName, array_values($keyNameList))) {
        $installer->getConnection()->addIndex(
            $table,
            'ix_' . $columnName,
            array($columnName),
            $columnDef['index']
        );
    }
}

$installer->endSetup();

Mage::setIsDeveloperMode($isDevMode);