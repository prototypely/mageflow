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
 * Mageflow_Connect_Model_Data_Collection
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Data_Collection extends Varien_Data_Collection
{

    /**
     * Adds functionality to Varien_Data_Collection for
     * adding collection of items to current collection
     *
     * @param Varien_Data_Collection $itemList
     *
     * @return Mageflow_Connect_Model_Data_Collection
     */
    public function addItems(Varien_Data_Collection $itemList)
    {
        foreach ($itemList->getItems() as $item) {
            $this->addItem($item);
        }
        return $this;
    }

}
