<?php

/**
 * Abstract.php
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
 * Mageflow_Connect_Model_Handler_Cms_Abstract
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
abstract class Mageflow_Connect_Model_Handler_Cms_Abstract
    extends Mageflow_Connect_Model_Handler_Abstract
{

    /**
     * @param Mageflow_Connect_Model_Interfaces_Changeitem $row
     * @return string|void
     */
    public function getPreview(Mageflow_Connect_Model_Interfaces_Changeitem $row)
    {
        $output = '';
        $content = json_decode($row->getContent());
        if ($content->title) {
            $output = $content->title;
        }
        return $output;
    }

    /**
     * @param Mage_Cms_Model_Block|Mage_Cms_Model_Page $model
     * @return stdClass
     */
    public function packData(Mage_Core_Model_Abstract $model)
    {
        $storeIdList = $model->getResource()->lookupStoreIds($model->getId());
        $storeCodeMap = $this->getStoreCodeMap(array_values($storeIdList));
        $c = $this->packModel($model);
        if(sizeof($storeCodeMap)<1){
            $storeCodeMap[] = 'admin';
        }
        $c->stores = array_values($storeCodeMap);
        return $c;
    }
}