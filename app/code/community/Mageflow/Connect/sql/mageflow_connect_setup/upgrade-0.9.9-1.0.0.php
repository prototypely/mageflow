<?php

/**
 * upgrade-0.9.9-1.0.0.php
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

$tableList = array(
    'core/website',
    'core/store',
    'core/store_group'
);

foreach ($tableList as $tableShortName) {
    $tableName = $installer->getTable($tableShortName);
    if (!$installer->getConnection()->tableColumnExists($tableName, 'mf_guid')) {
        $installer->getConnection()->addColumn($tableName, 'mf_guid', 'VARCHAR(64) NULL');
        $installer->getConnection()->addIndex($tableName, 'ix_mf_guid', array('mf_guid'));
    }
    $installer->getConnection()->commit();
}

$dataHelper = Mage::helper('mageflow_connect');
$storeList = Mage::getModel('core/store')->getCollection();


$installer->getConnection()->beginTransaction();
$adminWebsite = Mage::getModel('core/website')->load(Mage_Core_Model_Store::ADMIN_CODE, 'code');
$adminStore = Mage::getModel('core/store')->load(Mage_Core_Model_Store::ADMIN_CODE, 'code');

if (is_null($adminWebsite->getMfGuid())) {
    $adminWebsite->setMfGuid($dataHelper->randomHash(32));
    $adminWebsite->save();
}
if (is_null($adminStore->getMfGuid())) {
    $adminStore->setMfGuid($dataHelper->randomHash(32));
    $adminStore->save();
}
/**
 * @var Mage_Core_Model_Store $store
 */
foreach ($storeList->getItems() as $store) {
    if (is_null($store->getMfGuid())) {
        $store->setMfGuid($dataHelper->randomHash(32));
        $store->save();
    }

    $storeGroup = $store->getGroup();
    if (is_null($storeGroup->getMfGuid())) {
        $storeGroup->setMfGuid($dataHelper->randomHash(32));
        $storeGroup->save();
    }

    $website = $store->getWebsite();
    if (is_null($website->getMfGuid())) {
        $website->setMfGuid($dataHelper->randomHash(32));
        $website->save();
    }
}
$installer->getConnection()->commit();

$installer->endSetup();

Mage::setIsDeveloperMode($isDevMode);