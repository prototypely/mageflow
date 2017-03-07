<?php

/**
 * install-0.2.0.php
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

$table = $installer->getTable('mageflow_connect/performance_history');
if (!$installer->getConnection()->isTableExists($table)) {
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
            'request_path',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            array(
                'nullable' => false,
            ),
            'Request path'
        )
        ->addColumn(
            'memory',
            Varien_Db_Ddl_Table::TYPE_BIGINT,
            null,
            array(
                'nullable' => false,
            ),
            'Current memory usage in bytes'
        )
        ->addColumn(
            'sessions',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'nullable' => false,
            ),
            'Number of active sessions'
        )
        ->addColumn(
            'cpu_load',
            Varien_Db_Ddl_Table::TYPE_FLOAT,
            null,
            array(
                'nullable' => false,
            ),
            'Current CPU load'
        )
        ->addColumn(
            'created_at',
            Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            null,
            array(
                'nullable' => false
            ),
            'Creation time'
        )
        ->addIndex('ix_request_path', array('request_path'))
        ->addIndex('ix_created_at', array('created_at'))
        ->addIndex(
            'ix_request_path_created_at',
            array('request_path', 'created_at')
        );
    $installer->getConnection()->createTable($tableDef);
}
$installer->endSetup();