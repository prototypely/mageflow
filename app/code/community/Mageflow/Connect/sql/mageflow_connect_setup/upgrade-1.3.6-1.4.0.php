<?php

/**
 * upgrade-1.3.6-1.4.0.php
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

$tableList = array(
    'tax_calculation_rule',
    'tax_calculation_rate',
    'newsletter_template',
    'sales_order_status',
    'widget_instance',
    'customer_group',
    'design_change',
    'tax_class',
    'poll'
);

foreach ($tableList as $tableName) {

    $table = $installer->getTable($tableName);

    $connection = $installer->getConnection();
    if ($table && $connection->isTableExists($table)) {

        $columnList = array(
            'mf_guid' => array(
                'spec' => 'VARCHAR(64)',
                'index' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
            ),
            'created_at' => array(
                'spec' => 'DATETIME',
                'index' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
            ),
            'updated_at' => array(
                'spec' => 'DATETIME',
                'index' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
            ),
        );
        foreach ($columnList as $columnName => $columnSpec) {
            if (!$connection->tableColumnExists($table, $columnName)) {
                $connection->addColumn(
                    $table,
                    $columnName,
                    $columnSpec['spec']
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
                    $columnSpec['index']
                );
            }
        }
    }
}

$installer->endSetup();

Mage::setIsDeveloperMode($isDevMode);
