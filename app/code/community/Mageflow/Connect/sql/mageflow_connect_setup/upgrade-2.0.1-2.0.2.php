<?php

$isDevMode = Mage::getIsDeveloperMode();
Mage::setIsDeveloperMode(true);

/* @var $installer Mageflow_Connect_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$connection = $installer->getConnection();

$table = $installer->getTable('mageflow_connect/changeset_item');
if ($table && $connection->isTableExists($table)) {
    if ($connection->tableColumnExists($table, 'metainfo')) {
        $connection->changeColumn($table, 'metainfo', 'metainfo', 'TEXT');
    }
}

$table = $installer->getTable('mageflow_connect/changeset_item_cache');
if ($table && $connection->isTableExists($table)) {
    if ($connection->tableColumnExists($table, 'meta_info')) {
        $connection->changeColumn($table, 'meta_info', 'meta_info', 'TEXT');
    }
}

$installer->endSetup();

Mage::setIsDeveloperMode($isDevMode);