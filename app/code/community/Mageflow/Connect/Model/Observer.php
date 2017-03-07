<?php

/**
 * Observer.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Model_Observer
 * This class extends Mage_Admin_Model_Observer
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Observer extends
    Mage_Admin_Model_Observer
{

    /**
     * Class constructor
     */
    public function _construct()
    {
        parent::_construct();
    }

    /**
     * Collects each request's memory usage
     * to database table
     */
    public function collectMemoryUsage()
    {
        if (Mage::isInstalled()
            && Mage::app()->getStore()->getConfig(Mageflow_Connect_Model_System_Config::ENABLE_COLLECTING_MEMORY)
        ) {
            /**
             * @var Mageflow_Connect_Model_System_Info_Memory $performanceHistoryModel
             */
            $performanceHistoryModel = Mage::getModel(
                'mageflow_connect/system_info_performance'
            );
            $performanceHistoryModel->setRequestPath(
                Mage::app()->getFrontController()->getRequest()->getRequestUri()
            );
            $performanceHistoryModel->setMemory(memory_get_usage(true));
            $performanceHistoryModel->setSessions(
                Mage::getModel('mageflow_connect/system_info_session')
                    ->getNumberOfActiveSessions()
            );
            $now = new DateTime();
            $performanceHistoryModel->setCreatedAt($now->format('c'));
            $performanceHistoryModel->setCpuLoad(
                Mage::getModel('mageflow_connect/system_info_cpu')->getSystemLoad()
            );
            $performanceHistoryModel->save();
        }
    }

    /**
     * @return Mageflow_Connect_Helper_Type
     */
    public function getTypeHelper()
    {
        return Mage::helper('mageflow_connect/type');
    }

    /**
     * Saves changeset item for allowed types
     *
     * @param Varien_Event_Observer $observer
     */
    public function onSaveChangesetItem(Varien_Event_Observer $observer)
    {
        try {
            $e = $observer->getEvent();
            if ($e instanceof Varien_Event) {

                $o = $e->getData('object');

                $type = !is_null($o) ? get_class($o) : null;

                $this->log('Type: ' . $type);

                if (null !== $o && null !== $type
                    && (
                        $this->getTypeHelper()->isTypeEnabled($type)
                        || $o instanceof Mage_Core_Model_Config_Data
                    )
                    && !($o instanceof Mageflow_Connect_Model_Changeset_Item)
                ) {

                    // check, if we actually allow creating changeset
                    if ($o->getData('disable_creating_changeset')) {
                        return;
                    }

                    $this->log($this->getTypeHelper()->getHandlerClass($type, $o));

                    /**
                     * @var stdClass $type
                     */
                    $type = $this->getTypeHelper()->getType($type, $o);

                    /**
                     * @var Mageflow_Connect_Helper_Changeset $csHelper
                     */
                    $csHelper = Mage::helper('mageflow_connect/changeset');

                    $csItemModel = $csHelper->createChangesetFromItem($type, $o);

                    /**
                     * Save newly created changeset item MFGUID to registry
                     * so that other parts of the process can access it
                     */
                    if (!$o->isObjectNew()) {
                        Mage::register($o->getMfGuid(), $csItemModel->getMfGuid(), true);
                    }
                }
            }
        } catch (Exception $ex) {
            $this->log($ex->getMessage());
            $this->log($ex->getTraceAsString());
        }
    }

    public function onMoveCategory(Varien_Event_Observer $observer)
    {
        try {
            $e = $observer->getEvent();
            if ($e instanceof Varien_Event) {
                $category = $e->getData('category');
                Mage::dispatchEvent(
                    'save_changeset_item', array('object' => $category, 'original_event' => $e->getName())
                );
            }
        } catch (Exception $ex) {
            $this->log($ex->getMessage());
            $this->log($ex->getTraceAsString());
        }
    }

    /**
     * This generic event observer listens to vast majority of "model save" events.
     * It intercepts and fires new event for creating changeset.
     *
     * @param Varien_Event_Observer $observer
     */
    public function onModelSaveCommitAfter(Varien_Event_Observer $observer)
    {
        try {
            $e = $observer->getEvent();
            if ($e instanceof Varien_Event) {

                $this->log($e->getName());

                $dataArr = $e->getData();

                $o = null;
                if (isset($dataArr['object']) && is_object($dataArr['object'])) {
                    $o = $dataArr['object'];
                } elseif (isset($dataArr['data_object']) && is_object($dataArr['data_object'])) {
                    $o = $dataArr['data_object'];
                }

                Mage::dispatchEvent('save_changeset_item',
                    array('object' => $o, 'original_event' => $e->getName()));
            }
        } catch (Exception $ex) {
            $this->log($ex->getMessage());
            $this->log($ex->getTraceAsString());
        }
    }

    /**
     * Adds MageFlow data to every supported entity type
     *
     * @param Varien_Event_Observer $observer
     */
    public function onBeforeSave(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        $dataArr = $event->getData();

        $type = is_object($dataArr['object']) ? get_class($dataArr['object']) : null;

        $this->log($type);

        if (null !== $type && $this->getTypeHelper()->isTypeEnabled($type, $dataArr['object'])) {

            $now = new Zend_Date();

            $dataArr['object']->setUpdatedAt($now->toString('c'));

            $mfGuid = null;
            $mfGuid = $mfGuid = $dataArr['object']->getMfGuid();

            // retry getting mf_guid for entities that do not return it "nicely"
            if (is_null($mfGuid)) {
                if (method_exists($dataArr['object'], 'isObjectNew') && !$dataArr['object']->isObjectNew()) {
                    $handlerClass = $this->getTypeHelper()->getHandlerClass(
                        get_class($dataArr['object']), $dataArr['object']
                    );
                    if (class_exists($handlerClass, true)) {
                        $handler = Mage::getModel($handlerClass);
                        if (method_exists($handler, 'returnMfGuid')) {
                            $mfGuid = $handler->returnMfGuid($dataArr['object']);
                        }
                    }
                }
            }

            $objectIsAbstractModel = false;
            $objectIsNew = false;
            $objectMfGuidEmty = false;
            $originalMfGuidEmpty = false;

            if ($dataArr['object'] instanceof Mage_Core_Model_Abstract) {
                $objectIsAbstractModel = true;
            }

            if ($objectIsAbstractModel && ($dataArr['object']->isObjectNew() === true)) {
                $objectIsNew = true;
            }

            if ('' == trim($mfGuid)) {
                $objectMfGuidEmty = true;
            }

            if (trim($dataArr['object']->getOrigData('mf_guid')) == '') {
                $originalMfGuidEmpty = true;
            }

            $updateMfGuid = false;
            if ($objectIsAbstractModel && $objectIsNew && $objectMfGuidEmty) {
                $updateMfGuid = true;
            }

            if (!$objectIsNew && $objectMfGuidEmty && $originalMfGuidEmpty) {
                $updateMfGuid = true;
            }

            if (($dataArr['object'] instanceof Mage_Catalog_Model_Product) && $objectIsNew && !$objectMfGuidEmty) {
                $collection = Mage::getModel('catalog/product')
                    ->getCollection()
                    ->addFieldToFilter('mf_guid', $mfGuid);

                if ($collection->getSize() > 1) {
                    $updateMfGuid = true;
                }
            }

            if ($updateMfGuid) {
                $dataArr['object']->setCreatedAt($now->toString('c'));
                $dataArr['object']->setMfGuid(
                    Mage::helper('mageflow_connect')->randomHash(32)
                );
            }
        }
    }

    /**
     * Sets processing_attribute_set registry flag when attribute set is being saved
     *
     * @param Varien_Event_Observer $observer
     */
    private function setFlagForAttributeSet(Varien_Event_Observer $observer)
    {
        /**
         * @var Mage_Adminhtml_Catalog_Product_SetController $controller
         */
        $controller = $observer->getData('controller_action');
        if ($controller instanceof Mage_Adminhtml_Catalog_Product_SetController
            && $controller->getRequest()->getActionName() == 'save'
        ) {
            Mage::register('processing_attribute_set', true);
        }
    }

    public function onControllerActionPredispatch(Varien_Event_Observer $observer)
    {
        $this->setFlagForAttributeSet($observer);
        $this->activateMaintenanceMode($observer);

        return;
    }

    /**
     * This observer handles onControllerFrontInitBefore by Magento.
     * It checks for parameters and enables maintenance mode.
     * It let's through requests from IP-s that are in the developer whitelist.
     *
     * @param Varien_Event_Observer $observer
     */
    private function activateMaintenanceMode(Varien_Event_Observer $observer)
    {
        $store = Mage::app()->getStore();
        $storeIsAdmin = $store->isAdmin();
        $area = Mage::getDesign()->getArea();

        $maintenanceModeEnabled = (boolean)Mage::app()->getStore()->getConfig(
            Mageflow_Connect_Model_System_Config::SYSTEM_MAINTENANCE_MODE
        );
        if (
            $maintenanceModeEnabled
            && (!$storeIsAdmin
                && $area !== 'adminhtml')
        ) {
            $allowIps = Mage::app()->getStore()->getConfig(
                'dev/restrict/allow_ips'
            );
            if (!is_null($allowIps)) {
                $ipWhiteList = array_map('trim', explode(',', $allowIps));
                $ip = $_SERVER['REMOTE_ADDR'];
                if (is_array($ipWhiteList) && in_array($ip, $ipWhiteList)) {
                    return;
                }
            }
            include_once MAGENTO_ROOT . '/errors/503.php';
            exit();
        }
    }

    /**
     * @param $msg
     *
     * @return mixed
     */
    protected function log($msg)
    {
        return Mage::helper('mageflow_connect/log')->log($msg);
    }
}
