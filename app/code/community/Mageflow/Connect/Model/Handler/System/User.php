<?php

/**
 * User.php
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
 * Mageflow_Connect_Model_Handler_System_User
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_System_User
    extends Mageflow_Connect_Model_Handler_Abstract
{

    /**
     * update or create  from data array
     *
     * @param $data
     *
     * @return array|null
     */
    public function processData(array $data)
    {

        $data = isset($data[0]) ? $data[0] : $data;

        $itemModel = null;
        $message = null;
        $savedEntity = null;

        $itemModel = $this->findModel('admin/user', $data['mf_guid'], array('field' => 'username', 'value' => $data['username']));

        $data['user_id'] = $itemModel->getUserId();

        $roleList = array();

        foreach ($data['roles'] as $key => $roleData) {
            $roleEntity = $this->findRole($roleData);
            if (null !== $roleEntity) {
                $data['roles'][$key] = $roleEntity->getRoleId();
                if (isset($roleData['acl']) && sizeof($roleData['acl']) > 0) {

                    /**
                     * 1. delete old rules
                     * 2. add new rules
                     */

                    /**
                     * @var Mage_Admin_Model_Resource_Rules_Collection $ruleModelCollection
                     */
                    $ruleModelCollection = Mage::getModel('admin/rules')->getCollection();
                    $ruleModelCollection->addFieldToFilter('role_id', $roleEntity->getId());
                    /**
                     * @var Mage_Admin_Model_Rules $ruleModel
                     */
                    foreach ($ruleModelCollection as $ruleModel) {
                        $ruleModel->delete();
                    }
                    $ruleModelCollection->clear();
                    foreach ($roleData['acl'] as $acl) {
                        $ruleModel = Mage::getModel('admin/rules');
                        $ruleModel->setResourceId($acl['resource']);
                        $ruleModel->setPermission($acl['permission']);
                        $ruleModel->setRoleId($roleEntity->getId());
                        $ruleModel->setRoleType($roleEntity->getRoleType());
                        $ruleModel->setAssertId(0);
                        $ruleModelCollection->addItem($ruleModel);
                    }
                    $ruleModelCollection->save();
                }

            } else {
                $roleProcessor = Mage::getModel('Mageflow_Connect_Model_Handler_System_Role');
                $result = $roleProcessor->processData($data['roles'][$key]);
                if (isset($result['current_entity'])) {
                    $roleEntity = $this->findRole(array('mf_guid' => $result['current_entity']->mf_guid));
                    if (!is_null($roleEntity)) {
                        $roleList[] = $roleEntity->getRoleId();
                    }
                }
                unset($data['roles'][$key]);
            }
        }

        try {
            $data['disable_creating_changeset'] = true;
            $savedEntity = $this->saveItem($itemModel, $data);

            foreach ($roleList as $roleId) {
                $savedEntity->setData('role_id', $roleId);
                $savedEntity->add();
            }
            $savedEntity->setData('disable_creating_changeset', false);
            $savedEntity->save();

        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $this->log($ex->getMessage());
            $this->log($ex->getTraceAsString());
        }

        if ($savedEntity instanceof Mage_Admin_Model_User) {
            if (isset($data['roles'])) {
                $savedEntity->setRoleIds($data['roles'])
                    ->setRoleUserId($savedEntity->getUserId())
                    ->saveRelations();
            }
        }

        return $this->sendProcessingResponse($savedEntity, $message);
    }

    /**
     * Helper method to find role either by mfguid or name
     *
     * @param $data
     * @return null|Mage_Admin_Model_Role
     */
    private function findRole($data)
    {
        /**
         * @var Mage_Admin_Model_Resource_Roles_Collection $modelCollection
         */
        $modelCollection = Mage::getModel('admin/roles')->getCollection();
        if (isset($data['mf_guid'])) {
            $modelCollection->getSelect()->where('mf_guid=?', $data['mf_guid']);
        }
        if (isset($data['role_name'])) {
            $modelCollection->getSelect()->orWhere('role_name=?', $data['role_name']);
        }
        $model = $modelCollection->getFirstItem();
        if ($model instanceof Mage_Admin_Model_Role && $model->getId() > 0) {
            return $model;
        }
        return null;

    }

    /**
     * pack content
     *
     * @param Mage_Admin_Model_User $model
     *
     * @return stdClass
     */
    public function packData(Mage_Core_Model_Abstract $model)
    {
        if (isset($model['password_confirmation'])) {
            unset($model['password_confirmation']);
        }

        $c = $this->packModel($model);

        /**
         * @var Mage_Admin_Model_Resource_Role_Collection $roleCollection
         *
         * we get role info directly from request, as it might not be
         * updated in the db at current moment
         */
        $roles   = Mage::app()->getRequest()->getParam('roles', null);

        if (is_null($roles)) {
            $roles = $model->getRoles();
        }
        $roleCollection = Mage::getModel('admin/roles')->getCollection();
        $roleCollection->addFieldToFilter('role_id', array(array('in' => $roles)));

        $roleList = array();
        $rolePacker = $this->getDataProcessor(get_class(Mage::getModel('admin/role')));
        foreach ($roleCollection as $rolesModel) {
            $roleList[] = $rolePacker->packData($rolesModel);
        }
        $c->roles = $roleList;
        return $c;
    }

    /**
     * @param Mageflow_Connect_Model_Interfaces_Changeitem $row
     * @return string
     */
    public function getPreview(Mageflow_Connect_Model_Interfaces_Changeitem $row)
    {
        $output = '';
        $content = json_decode($row->getContent());
        if ($content->username) {
            $output = $content->username;
        }
        return $output;
    }
}