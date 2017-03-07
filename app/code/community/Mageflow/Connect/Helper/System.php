<?php

/**
 * System.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Helper_System
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Helper_System extends Mageflow_Connect_Helper_Data
{

    /**
     * Clean all Magento caches
     */
    public function cleanCache()
    {
        try {
            $allTypes = Mage::app()->getCacheInstance()->getTypes();
            foreach ($allTypes as $type => $blah) {
                Mage::app()->getCacheInstance()->cleanType($type);
            }
        } catch (Exception $e) {
            $this->log($e->getMessage());
            $this->log($e->getTraceAsString());
        }
    }

    /**
     * return current cache setting
     * or set cache settings to $settingsArray
     *
     * @param array $settingsArray
     *
     * @return array
     */
    public function cacheSettings($settingsArray = null)
    {
        $currentSettingsArray = Mage::getResourceSingleton('core/cache')->getAllOptions();

        if (is_null($settingsArray)) {
            return $currentSettingsArray;
        }

        if (array_key_exists('all', $settingsArray)) {
            foreach ($currentSettingsArray as $key => $setting) {
                $currentSettingsArray[$key] = $settingsArray['all'];
            }
        } else {
            foreach ($settingsArray as $key => $setting) {
                $currentSettingsArray[$key] = $setting;
            }
        }

        $this->cleanCache();

        Mage::app()->saveUseCache($currentSettingsArray);

        $this->cleanCache();

        $this->log(
            sprintf(
                'Applied cache settings: %s',
                print_r($currentSettingsArray, true)
            )
        );
        return $currentSettingsArray;

    }
}