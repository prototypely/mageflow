<?php

/**
 * Poll.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com)
 * @license    http://magelfow.com/licenses/mfx/eula.txt MageFlow Extension End User License (EULA)
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Model_Handler_Cms_Poll
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com)
 * @license    http://magelfow.com/licenses/mfx/eula.txt MageFlow Extension End User License (EULA)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_Cms_Poll extends Mageflow_Connect_Model_Handler_Abstract
{
    /**
     * @param array $data
     *
     * @return array
     * @throws Exception
     */
    public function processData(array $data = array())
    {
        $data = isset($data[0]) ? $data[0] : $data;
        $savedEntity = null;
        $message = 'success';
        $model = null;

        if (isset($data['store'])) {
            $storeIdList = $this->getStoreIdListByCodes(array($data['store']));
            $data['store_id'] = implode('',$storeIdList);
            unset($data['store']);
        } else {
            $data['store_id'] = 0;
        }

        if (isset($data['stores'])) {
            $data['store_ids'] = $this->getStoreIdListByCodes($data['stores']);
            unset($data['stores']);
        }

        /**
         * @var Mage_Poll_Model_Poll $model
         */
        $modelByIdentifier = Mage::getModel('poll/poll')
            ->load($data['poll_title'], 'poll_title');

        $modelByMfGuid = Mage::getModel('poll/poll')
            ->load($data['mf_guid'], 'mf_guid');

        if ($modelByIdentifier->getPollId()) {
            $model = $modelByIdentifier;
        }
        if ($modelByMfGuid->getPollId()) {
            $model = $modelByMfGuid;
        }

        if (null === $model) {
            $model = Mage::getModel('poll/poll');
        }

        if ($model->getPollId()>0) {
            $data['poll_id'] = $model->getPollId();
        }

        try {
            $savedEntity = $this->saveItem($model, $data);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $this->log($ex->getMessage());
            $this->log($ex->getTraceAsString());
        }

        return $this->sendProcessingResponse($savedEntity, $message);
    }

    /**
     * @param Mage_Core_Model_Abstract $model
     *
     * @return stdClass
     */
    public function packData(Mage_Core_Model_Abstract $model)
    {
        $c = $this->packModel($model);
        $storeId = $model->getData('store_id');
        $storeEntity = Mage::getModel('core/store')->load($storeId);
        $c->store = $storeEntity->getCode();

        $storeCodeMap = $this->getStoreCodeMap(array_values($model->getStoreIds()));
        $c->stores = array_values($storeCodeMap);

        $answersCollection = Mage::getModel('poll/poll_answer')
            ->getCollection()
            ->addFieldToFilter(array('poll_id'),
                array(
                    array(
                        'eq'=>$model->getPollId()
                    ),
                )
            );
        $answersArray = array();
        foreach($answersCollection as $answerEntity) {
            $this->log(print_r($answerEntity, true));
            $answersArray[] = array(
                'answer_title' => $answerEntity->getData('answer_title'),
                'answer_order' => $answerEntity->getData('answer_order')
            );
        }
        $c->answers = $answersArray;

        return $c;
    }

    /**
     * @param Mageflow_Connect_Model_Interfaces_Changeitem $item
     *
     * @return string
     */
    public function getPreview(Mageflow_Connect_Model_Interfaces_Changeitem $item)
    {
        $out = '';

        $object = json_decode($item->getContent());
        if ($object->poll_title) {
            $out = $object->poll_title;
        }
        return $out;
    }

    /**
     * @param Mage_Core_Model_Abstract $model
     * @param                          $data
     *
     * @return Mage_Core_Model_Abstract|object
     * @throws Exception
     */
    public function saveItem($model, $data)
    {
        $model = parent::saveItem($model, $data);

        if (!isset($data['answers']) || !is_array($data['answers'])) {
            return $model;
        }

        $pollId = $model->getPollId();

        /*
         * we need to get existing answers, if we are updating
         */
        $answersCollection = Mage::getModel('poll/poll_answer')
            ->getCollection()
            ->addFieldToFilter(array('poll_id'),
                array(
                    array(
                        'eq'=>$pollId
                    ),
                )
            );
        foreach($answersCollection as $answerEntity) {
            $answerEntity->delete();
        }

        foreach($data['answers'] as $answerData) {
            $answerModel = Mage::getModel('poll/poll_answer');
            $answerData['poll_id'] = $pollId;
            $answerModel->setData($answerData);
            $answerModel->save();
        }

        return $model;
    }
} 