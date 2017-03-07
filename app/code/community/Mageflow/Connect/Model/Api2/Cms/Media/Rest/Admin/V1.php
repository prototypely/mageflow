<?php

/**
 * V1.php
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
 * Mageflow_Connect_Model_Api2_Cms_Media_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Cms_Media_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'cms_media';


    /**
     * retrieve collection
     *
     * @return array
     */
    public function _retrieveCollection()
    {
        $out = array();
        /**
         * @var Mageflow_Connect_Model_Media_Index $mediaIndexModel
         */
        foreach (
            $this->getWorkingModel()->getCollection() as $mediaIndexModel
        ) {
            $out[] = $this->packItem($mediaIndexModel);
        }
        return $out;
    }

    /**
     * pack item
     *
     * @param Mageflow_Connect_Model_Media_Index $mediaIndexModel
     *
     * @return array
     */
    private function packItem($mediaIndexModel)
    {
        $a = array();
        $a['basename'] = $mediaIndexModel->getBasename();
        $a['path'] = $mediaIndexModel->getPath();
        $a['mtime'] = $mediaIndexModel->getMtime();
        $a['size'] = $mediaIndexModel->getSize();
        $a['type'] = $mediaIndexModel->getType();
        $a['mf_guid'] = $mediaIndexModel->getMfGuid();
        return $a;
    }

    /**
     * GET request to retrieve a single CMS media index item by its MF GUID
     *
     * @return array|mixed
     */
    public function _retrieve()
    {
        $this->log($this->getRequest()->getParams());
        $mfGuid = $this->getRequest()->getParam('key', -1);
        $out = array();

        $item = $this->getDataProcessor()->findItem($mfGuid);
        if (null !== $item) {
            $out[] = $this->packItem($item);
        }

        return $out;
    }


}
