<?php

/**
 * upgrade-1.4.9-1.5.0.php
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

$connection = $installer->getConnection();

$tableName = 'mageflow_connect/changeset_item';

$table = $installer->getTable($tableName);
if ($table && $connection->isTableExists($table)) {
    $columnName = 'item_mf_guid';
    if (!$connection->tableColumnExists($table, $columnName)) {
        $connection->addColumn(
            $table,
            $columnName,
            'VARCHAR(64)'
        );
    }
    
    $columnName = 'is_current';
    if (!$connection->tableColumnExists($table, $columnName)) {
        $connection->addColumn(
            $table,
            $columnName,
            'BOOLEAN'
        );
    }    
}
$installer->endSetup();

Mage::setIsDeveloperMode($isDevMode);