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

$tableName = 'core/email_template';

$table = $installer->getTable($tableName);
$connection = $installer->getConnection();
if ($table && $connection->isTableExists($table)) {
    $columnName = 'mf_guid';
    if(!$connection->tableColumnExists($table, $columnName)){
        $connection->addColumn(
            $table,
            $columnName,
            'VARCHAR(64)'
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
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        );
    }
}
$installer->endSetup();

Mage::setIsDeveloperMode($isDevMode);