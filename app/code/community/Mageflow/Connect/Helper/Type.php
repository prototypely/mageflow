<?php

/**
 * Type.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Helper_Type
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Helper_Type extends Mageflow_Connect_Helper_Data {

    private $types = array();

    /**
     * function to return correct version of this array
     * to be extended in different mfx versions
     * 
     * @return type
     */
    protected function getTypeToUrlMap() {
        return Mage::getModel('mageflow_connect/system_config')->getTypeToUrlMap();
    }

    /**
     * function to return correct version of this array
     * to be extended in different mfx versions
     * 
     * @return type
     */
    protected function getTypeMap() {
        return Mage::getModel('mageflow_connect/system_config')->getTypeMap();
    }
    
    /**
     * is enabled
     *
     * @param        $typeName
     * @param object $instance
     *
     * @return bool
     */
    public function isTypeEnabled($typeName, $instance = null) {
        $type = $this->getType($typeName, $instance);
        
        $this->log(print_r($type, true));

        if (is_null($type) || !$type->enabled) {
            return false;
        }

        $typeUrl = null;
        $configPath = null;
        $configSetting = null;

        if (!empty($type->name)) {
            $typeShortName = str_replace('_', ':', $type->name);
            $typeUrlMap = $this->getTypeToUrlMap();
            $typeMap = $this->getTypeMap();

            if (isset($typeUrlMap[$typeShortName])) {
                $typeUrl = $typeUrlMap[$typeShortName];
                $configPath = $typeMap[$typeUrl]['config'];
                $configSetting = Mage::app()->getStore()->getConfig('mageflow_connect/enabled_types/' . $configPath);
            }
        }
        // if we have identified a type successfully and have a setting for it

        if (!is_null($typeUrl) && !is_null($configSetting)) {
            if ($configSetting) {
                // we know the type and allow it
                //$this->log('allowed by setting');
                return true;
            } else {
                //we know the type but forbid it
                //$this->log('disabled by setting');
                return false;
            }
        }

        if (Mage::app()->getStore()->getConfig(Mageflow_Connect_Model_System_Config::ENABLE_OTHER)) {
            //$this->log('other is enabled');
            return true;
        }
        
        //we do not know the type and other is disabled
            //$this->log('other is enabled');
        return false;
    }

    /**
     * @param string $typeName
     * @param object $instance
     *
     * @return stdClass|null
     */
    public function getType($typeName, $instance = null) {
//        $this->log('SEARCHING TYPE: ' . $typeName);
        foreach ($this->getTypes() as $type) {
            if (
                    $type->name == $typeName || $type->type == $typeName || $type->short == $typeName || in_array($typeName, $type->aliases, true) || $this->isSubclassOf(
                            $instance, array_merge(array($type->type, $type->name, $type->short), $type->aliases)
                    )
            ) {
                return $type;
            }
        }
        return null;
    }

    /**
     * Checks whether given instance is subclass
     * of any class names in given array
     *
     * @param       $instance
     * @param array $classNameList
     *
     * @return bool
     */
    private function isSubclassOf($instance, $classNameList = array()) {
        foreach ($classNameList as $className) {
            if (is_subclass_of($instance, $className)) {
                return true;
            }
        }
        return false;
    }

    /**
     * get types
     *
     * @return array|mixed|string
     */
    public function getTypes() {
        if (sizeof($this->types) == 0) {
            $cacheId = md5(__METHOD__);
            $cache = Mage::app()->getCache();
            if ($cache->load($cacheId)) {
                $this->types = unserialize($cache->load($cacheId));
            } else {
                /**
                 * @var Mage_Api2_Model_Config $configModel
                 */
                $configModel = Mage::getSingleton('api2/config');
                /**
                 * @var Mage_Core_Model_Config $coreConfigModel
                 */
                $coreConfigModel = Mage::app()->getConfig();
                $typeNodeList = $configModel->getResourceGroup('mageflow');

                $types = array();
                /**
                 * @var Mage_Core_Model_Config_Element $typeNode
                 */
                foreach ($typeNodeList as $typeNodeArr) {
                    /**
                     * @var Mage_Core_Model_Config_Element $typeNode
                     */
                    foreach ($typeNodeArr as $name => $typeNode) {
                        $xpathStr = sprintf(
                                'privileges/*/update[text()=1]|privileges/*/create[text()=1]|privileges/*/delete[text()=1]', $name
                        );
                        $privileges = $typeNode->xpath($xpathStr);
                        $isReadOnlyType = sizeof($privileges) <= 0;

                        $isEnabled = (null == $typeNode->getAttribute('enabled') || $typeNode->getAttribute('enabled') != 'false') ? true : false;

                        $shortType = (string) $typeNode->working_model;
                        $shortHandlerClass = (string) $typeNode->handler;
                        $typeData = new stdClass();
                        $typeData->name = $name;
                        $typeData->short = $shortType;
                        $typeData->table = isset($typeNode->table) ? (string) $typeNode->table : null;
                        $typeData->type = '';
                        $className = $coreConfigModel->getModelClassName($shortType);
                        //NOTE yes, we are aware of the evil of supressing errors with @:)
                        if (@class_exists($className, true)) {
                            $typeInstance = Mage::getModel($shortType);
                            $typeData->type = (is_object($typeInstance)) ? get_class($typeInstance) : '';
                        }

                        $collection = trim((string) $typeNode->collection);
                        $typeData->collection = ('' != $collection) ? $collection : $typeData->type;

                        $typeData->read_only = $isReadOnlyType;
                        $typeData->enabled = $isEnabled;
                        $typeData->index_enabled = isset($typeNode->index_enabled) ? ($typeNode->index_enabled != 'false') : true;
                        $helperInstance = ($shortHandlerClass != '') ? Mage::getModel($shortHandlerClass) : null;
                        $typeData->handler = (is_object($helperInstance)) ? get_class($helperInstance) : '';
                        $aliasNodeArr = $typeNode->xpath('aliases/*');
                        $aliasArr = array();
                        foreach ($aliasNodeArr as $aliasNode) {
                            $aliasArr[] = $aliasNode->getName();
                        }
                        $typeData->aliases = $aliasArr;

                        $types[$name] = $typeData;
                    }
                }
                $cache->save(serialize($types), $cacheId);
                $this->types = $types;
            }
        }
        return $this->types;
    }

    /**
     * Returns list with enabled types
     *
     * @return array
     */
    public function getEnabledTypes() {
        $out = array();
        foreach ($this->getTypes() as $typeName => $type) {
            if ($type->enabled) {
                $out[$typeName] = $type;
            }
        }
        return $out;
    }

    /**
     * This method returns list of types that
     * MageFlow supports.
     * NB! This list may change over MFx version changes.
     *
     * @return array
     */
    public function getSupportedTypes() {
        $typeList = $this->getTypes();
        $supportedTypes = array_keys($typeList);
        return $supportedTypes;
    }

    /**
     * convert long entity type to short
     *
     * @param $typeName
     *
     * @return string
     */
    public function convertTypeToShort($typeName) {
        $type = $this->getType($typeName);
        return $type->short;
    }

    /**
     * Returns packer class name for type.
     * It's specified in api2.xml for each type that is supported
     * by MageFlow and that is not read only type
     *
     * @param string $typeName
     * @param object $instance
     *
     * @return string
     */
    public function getHandlerClass($typeName, $instance = null) {
        $type = $this->getType($typeName, $instance);
        if (null !== $type) {
            return $type->handler;
        }
        return '';
    }

    /**
     * Returns array with type's fields
     *
     * @param $typeName
     *
     * @return array
     */
    public function getFieldList($typeName) {
        $type = $this->getType($typeName);

        $out = array();
        /**
         * @var Mage_Api2_Model_Config $configModel
         */
        $configModel = Mage::getSingleton('api2/config');
        $attributeNodeList = $configModel->getXpath('/*//resources/' . $type->name . '/attributes/*');
        foreach ($attributeNodeList as $node) {
            $out[] = $node->getName();
        }
//sort field list in order to give developers some visual cue
        sort($out);
        return $out;
    }

    /**
     * Returns an array with type's significant fields.
     * Significant fields are marked in XML as significant.
     * Significant fields are used for checksum calculations
     *
     * @param string $typeName
     *
     * @return array
     */
    public function getSignificantFieldList($typeName) {
        $nonSignificantFields = array(
            'created_at',
            'updated_at',
            'id',
            'entity_id',
            'mf_guid',
            'deploymentpackage',
            'attributes',
            'attribute_group_id',
            'attribute_set_id',
            'attribute_id',
            'modified',
            'logdate',
            'created',
            'lognum'
        );
        $type = $this->getType($typeName);
        $out = array();
        if (is_object($type)) {
            /**
             * @var Mage_Api2_Model_Config $configModel
             */
            $configModel = Mage::getSingleton('api2/config');
            $attributeNodeList = $configModel->getXpath('/*//resources/' . $type->name . '/attributes/*');
            foreach ($attributeNodeList as $node) {
                if (!in_array($node->getName(), $nonSignificantFields)) {
                    $out[] = $node->getName();
                }
            }
            sort($out);
        }
        return $out;
    }

}
