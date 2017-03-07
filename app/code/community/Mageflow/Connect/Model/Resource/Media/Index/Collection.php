<?php

/**
 * Collection.php
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
 * Mageflow_Connect_Model_Resource_Media_Index_Collection
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Resource_Media_Index_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    /**
     * Initialize resource model collection
     */
    protected function _construct()
    {
        $this->_init('mageflow_connect/media_index');
    }

    /**
     * Checks if file on the disk has changes compared
     * to file in index
     *
     * @param $file
     *
     * @return bool
     */
    public function fileIsCurrent($file)
    {
        $foundFile = $this->findFile($file);
        return (
            $foundFile !== null
            && $foundFile->getMtime() == $file->getMtime()
            && $foundFile->getSize() == filesize($file->getFilename())
        );
    }

    /**
     * Searches for file from Media Index by its ID (hash)
     *
     * @param $file
     *
     * @return Mageflow_Connect_Model_Media_Index
     */
    public function findFile($file)
    {
        /**
         * @var Mageflow_Connect_Model_Media_Index $item
         */
        foreach ($this->getItems() as $item) {
            if ($item->getHash() == $file->getId()) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Checks if given file exists in current Media Index
     *
     * @param $file
     *
     * @return bool
     */
    public function fileExists($file)
    {
        return $this->findFile($file) !== null;
    }
}
