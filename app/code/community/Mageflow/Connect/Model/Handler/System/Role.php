<?php

/**
 * Role.php
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
 * Mageflow_Connect_Model_Handler_System_Role
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_System_Role extends Mageflow_Connect_Model_Handler_Abstract
{
    /**
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function processData(array $data)
    {
        $data = isset($data[0]) ? $data[0] : $data;


        $message = null;
        $savedEntity = null;
        /**
         * @var Mage_Admin_Model_Resource_Roles_Collection $modelCollection
         */
        $modelCollection = Mage::getModel('admin/roles')->getCollection();
        $modelCollection->getSelect()->reset('where');
        $modelCollection->addFieldToFilter(
            array('mf_guid', 'role_name'),
            array(
                array(
                    'eq' => $data['mf_guid']
                ),
                array(
                    'eq' => $data['role_name']
                )
            )
        );
        if (isset($data['role_type'])) {
            $modelCollection->addFieldToFilter('role_type', $data['role_type']);
        }

        if (!is_null($data['parent_id'])) {
            $parentModel = Mage::getModel('admin/role')->load($data['parent_id'], 'mf_guid');
            $data['parent_id'] = $parentModel->getId();
        }
        $idList = $modelCollection->getAllIds();

        if (isset($data['user_id'])) {
            $userModel = Mage::getModel('admin/user')->load($data['user_id'], 'mf_guid');
            $data['user_id'] = $userModel->getId();
        }

        if (sizeof($idList) > 0) {
            $model = Mage::getModel('admin/role')->load($idList[0]);
        } else {
            $model = Mage::getModel('admin/role');
        }

        try {
            if (isset($data['mf_guid'])) {
                $model->setMfGuid($data['mf_guid']);
                $model->save();
            }
            $model->setData(array_merge($model->getData(), $data));
            $savedEntity = $model->save();
            if (isset($data['acl']) && sizeof($data['acl']) > 0) {

                /**
                 * 1. delete old rules
                 * 2. add new rules
                 */

                /**
                 * @var Mage_Admin_Model_Resource_Rules_Collection $ruleModelCollection
                 */
                $ruleModelCollection = Mage::getModel('admin/rules')->getCollection();
                $ruleModelCollection->addFieldToFilter('role_id', $savedEntity->getId());
                /**
                 * @var Mage_Admin_Model_Rules $ruleModel
                 */
                foreach ($ruleModelCollection as $ruleModel) {
                    $ruleModel->delete();
                }
                $ruleModelCollection->clear();
                foreach ($data['acl'] as $acl) {
                    $ruleModel = Mage::getModel('admin/rules');
                    $ruleModel->setResourceId($acl['resource']);
                    $ruleModel->setPermission($acl['permission']);
                    $ruleModel->setRoleId($savedEntity->getId());
                    $ruleModel->setRoleType($savedEntity->getRoleType());
                    $ruleModel->setAssertId(0);
                    $ruleModelCollection->addItem($ruleModel);
                }
                $ruleModelCollection->save();
            }
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $this->log($ex->getMessage());
            $this->log($ex->getTraceAsString());
        }

        return $this->sendProcessingResponse($savedEntity, $message);
    }

    /**
     * @param \Mage_Admin_Model_Role $model
     * @return stdClass
     */
    public function packData(Mage_Core_Model_Abstract $model)
    {
        $c = $this->packModel($model);
        if ($model->getParentId() > 0) {
            $parentModel = Mage::getModel('admin/roles')->load($model->getParentId());
            $c->parent_id = $parentModel->getMfGuid();
        } else {
            unset($c->parent_id);
        }
        if ($model->getUserId() > 0) {
            $userModel = Mage::getModel('admin/user')->load($model->getUserId());
            if ($userModel->getMfGuid()) {
                $c->user_id = $userModel->getMfGuid();
            }
        } else {
            unset($c->user_id);
        }

        /**
         * as we can not rely on rules being already updated in db,
         * we take the info from the request and construct the list here
         */
        $resourceList = Mage::getModel('admin/roles')->getResourcesList2D();
        $paramResources = Mage::app()->getRequest()->getParam('resource', null);
        $resources = array();
        $aclByDb = array();
        $aclByRequest = array();
        if (!is_null($paramResources)) {
            $resources   = explode(',', $paramResources);

            foreach ($resourceList as $resourceName) {
                $row = new stdClass();
                $row->permission  = (in_array($resourceName, $resources) ? 'allow' : 'deny');
                $row->resource = trim($resourceName, '/');
                $aclByRequest[] = $row;
            }
        } else {
            $ruleCollection = Mage::getModel('admin/rules')->getCollection()
                ->addFieldToFilter('role_id', $model->getRoleId());
            foreach($ruleCollection as $ruleEntity) {
                $resources[] = $ruleEntity->getResourceId();
                $row = new stdClass();
                $row->permission  = $ruleEntity->getPermission();
                $row->resource = $ruleEntity->getResourceId();
                $aclByDb[] = $row;
            }
        }

        if (count($aclByDb) > 0) {
            $c->acl = $aclByDb;
        } else {
            $c->acl = $aclByRequest;
        }

        return $c;
    }

    public function getPreview(Mageflow_Connect_Model_Interfaces_Changeitem $item)
    {
        $content = json_decode($item->getContent());
        return $content->role_name;
    }
}
