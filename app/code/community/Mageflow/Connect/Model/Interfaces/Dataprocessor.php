<?php

/**
 * Dataprocessor.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Model_Interfaces_Dataprocessor
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
interface Mageflow_Connect_Model_Interfaces_Dataprocessor
{

    /**
     * Method that packs data to a changeset item
     *
     * @param Mage_Core_Model_Abstract $model
     * @return mixed
     */
    public function packData(Mage_Core_Model_Abstract $model);

    /**
     * Method that processes incoming data
     * and creates a Magento object/entity from it
     *
     * @param array $data
     * @return array
     */
    public function processData(array $data);

    /**
     * This method returns preview of data for pull and push grids
     *
     * @param Mageflow_Connect_Model_Interfaces_Changeitem $row
     * @return string
     */
    public function getPreview(Mageflow_Connect_Model_Interfaces_Changeitem $row);

    /**
     * Validates model before save
     *
     * @param Mage_Core_Model_Abstract $model
     * @return boolean
     */
    public function validate(Mage_Core_Model_Abstract $model);

    /**
     * @param $typeName
     * @param string $mfGuid
     * @param array $identifier
     *
     * @return Mage_Core_Model_Abstract
     */
    public function findModel($typeName, $mfGuid = null, $identifier = array());

    /**
     * Calculates checksum over significant fields of given model
     *
     * @param Mage_Core_Model_Abstract $model
     * @return mixed
     */
    public function calculateChecksum(Mage_Core_Model_Abstract $model);

    /**
     * Reindex current data type
     *
     * @param stdClass $typeDef
     * @param int $limit
     * @return int
     */
    public function reindex($typeDef, $limit = -1);
} 