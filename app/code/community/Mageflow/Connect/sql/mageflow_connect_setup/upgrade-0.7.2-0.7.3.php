<?php

/**
 * upgrade-0.7.2-0.7.3.php
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

/**
 * This update script adds mf_guid value to catalog/category
 */
$isDevMode = Mage::getIsDeveloperMode();
Mage::setIsDeveloperMode(true);

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();
$collection = Mage::getModel('catalog/category')->getCollection();
$collection->addFieldToFilter('parent_id', 0);
$collection->load();
$absoluteRoot = $collection->getFirstItem();

$collection = Mage::getModel('catalog/category')->getCollection();
$collection->addFieldToFilter('parent_id', $absoluteRoot->getId());
$collection->load();

foreach ($collection as $categoryEntity) {
    if ($categoryEntity->getParentId() == $absoluteRoot->getId()) {
        $mfguid = md5($categoryEntity->getPath());
        $categoryEntity->setMfGuid($mfguid);
        $categoryEntity->save();
    }
}

$setup->endSetup();

Mage::setIsDeveloperMode($isDevMode);