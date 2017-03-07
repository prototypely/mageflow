<?php

/**
 * Template.php
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
 * Mageflow_Connect_Model_Handler_Newsletter_Template
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_Newsletter_Template
    extends Mageflow_Connect_Model_Handler_Abstract
{
    /**
     * update or create adminhtml/email_template from data array
     *
     * @param $data
     *
     * @return array
     */
    public function processData(array $data)
    {
        $data = isset($data[0]) ? $data[0] : $data;

        $model = null;
        $message = 'success';
        $savedEntity = null;

        $modelByIdentifier = Mage::getModel('newsletter/template')
            ->load($data['template_code'], 'template_code');

        $modelByMfGuid = Mage::getModel('newsletter/template')
            ->load($data['mf_guid'], 'mf_guid');

        if ($modelByIdentifier->getTemplateId()) {
            $model = $modelByIdentifier;
        }
        if ($modelByMfGuid->getTemplateId()) {
            $model = $modelByMfGuid;
        }

        if (null === $model) {
            $model = Mage::getModel('newsletter/template');
        }

        if (isset($data['mf_guid']) && $model->getTemplateId()>0) {
            $model->setMfGuid($data['mf_guid']);
            $model->save();
        }

        $data['template_id'] = $model->getTemplateId();

        try {
            $savedEntity = $this->saveItem($model, $data);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->log($e->getMessage());
            $this->log($e->getTraceAsString());
        }

        return $this->sendProcessingResponse($savedEntity, $message);
    }

    /**
     * @param Mage_Adminhtml_Model_Email_Template $model
     *
     * @return stdClass
     */
    public function packData(Mage_Core_Model_Abstract $model)
    {
        $c = $this->packModel($model);
        return $c;
    }

    /**
     * @param Mageflow_Connect_Model_Interfaces_Changeitem $row
     * @return string|void
     */
    public function getPreview(Mageflow_Connect_Model_Interfaces_Changeitem $row)
    {
        $output = '';
        $content = json_decode($row->getContent());
        if ($content->template_code) {
            $output = $content->template_code;
        }
        return $output;
    }

}