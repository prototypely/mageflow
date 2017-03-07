<?php

/**
 * Media.php
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
 * Mageflow_Connect_Model_Handler_Cms_Media
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_Cms_Media
    extends Mageflow_Connect_Model_Handler_Abstract
{

    /**
     * find item
     *
     * @param $mfGuid
     *
     * @return Mageflow_Connect_Model_Media_Index
     */
    private function findItem($mfGuid)
    {
        $collection = $this->getWorkingModel()->getCollection();
        $collection->addFilter('mf_guid', $mfGuid);
        $collection->load();
        if ($collection->getFirstItem() instanceof
            Mageflow_Connect_Model_Media_Index
            && $collection->getFirstItem()->getId() > 0
        ) {
            return $collection->getFirstItem();
        }
        return null;
    }

    /**
     * Create changeset item from Mageflow_Connect_Model_Media_Index
     *
     * @param $model
     *
     * @return array|void
     */
    public function packData(Mage_Core_Model_Abstract $model)
    {
        $this->log(print_r($model, true));
        $model['hex'] = bin2hex(file_get_contents($model['filename']));
        return $model;
    }

    /**
     * update or create CMS Media
     *
     * @param $data
     *
     * @return array
     */
    public function processData(array $data)
    {
        $model = $this->findModel('mageflow_connect/media_index', $data['mf_guid']);

        $filePath
            = Mage::getBaseDir('base') . '/' . ltrim($model->getPath(), '/');
        $this->log(
            'Saving file to ' . $filePath
        );
        $dirPath = dirname($filePath);
        if (!file_exists($dirPath)) {
            @mkdir($dirPath, 0777, true);
            $this->logPhpError(error_get_last());
        }
        @file_put_contents($filePath, pack('H*', $data['hex']));
        $this->logPhpError(error_get_last());

        $model->setMtime(time());
        $model->setSize(filesize($filePath));
        $model->save();
        $this->log($model);
        $this->_successMessage(
            'Created media file',
            200,
            $this->packItem($model)
        );

        return $this->sendProcessingResponse($model);
    }

    /**
     * @param Mageflow_Connect_Model_Interfaces_Changeitem $row
     * @return string
     */
    public function getPreview(Mageflow_Connect_Model_Interfaces_Changeitem $row)
    {
        $output = '';
        $content = json_decode($row->getContent());
        if (null !== $content->basename) {
            $output = sprintf(
                "%s (%s KB)", $content->basename,
                round(filesize($content->filename) / 1024)
            );
        }
        return $output;
    }
}