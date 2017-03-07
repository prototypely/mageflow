<?php

/**
 * Abstract.php
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
 * Mageflow_Connect_Model_Api2_Abstract
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Abstract extends Mage_Api2_Model_Resource
{

    const ACTION_TYPE_INFO = 'info';
    const STATUS_SUCCESS = 0;
    const STATUS_ERROR = 1;

    /**
     * version
     *
     * @var int
     */
    protected $_version = 1;

    /**
     * entity fields
     *
     * @var array
     */
    protected $_entityFields = array();

    /**
     * Class constructor
     */
    public function __construct()
    {
        //include Mageflow client lib and its autoloader
        @include_once 'Mageflow/Connect/Module.php';
        $m = new \Mageflow\Connect\Module();

        return $this;
    }

    /**
     * Retrieve information about customer
     *
     * @throws Mage_Api2_Exception
     * @return array
     */
    public function _retrieve()
    {
        return array();
    }

    /**
     * Returns information about concrete resource:
     * - type
     * - attributes
     * - other info (if available)
     *
     * @return Array Resource info
     */
    public function _getResourceInfo()
    {
        $out = array();
        $out['resource_url']
            = '/api/' . $this->getApiType() . '/' . str_replace(
                '_',
                '/',
                $this->getResourceType()
            );
        $out['resource_type'] = $this->_resourceType;
        $routes = $this->getConfig()->getNode(
            'resources/' . $this->_resourceType . '/routes'
        );
        $routesArr = array();
        foreach ($routes->children() as $name => $route) {
            $item = array(
                'name' => $name,
                'route' => (string)$route->route,
                'usage' => $this->getUsageInfo($this->_resourceType, $route->action_type)
            );
            $routesArr[] = $item;
        }
        $out['attributes'] = $this->getAvailableAttributesFromConfig();
        $out['routes'] = $routesArr;
        return $out;
    }

    /**
     * Extracts usage info from actual class
     * @param $resourceType
     * @param $routeAction
     * @throws Exception
     * @throws Mage_Api2_Exception
     * @return string
     */
    private function getUsageInfo($resourceType, $routeAction)
    {
        $nodeList = $this->getConfig()->getXpath(sprintf('/*//api2/resources/%s/model/node()', $resourceType));
        if (is_array($nodeList) && isset($nodeList[0])) {
            $resourceClass = (string)$nodeList[0];
            /**
             * @var Mage_Api2_Model_Dispatcher $dispatcher
             */
            $dispatcher = Mage::getSingleton('api2/dispatcher');
            $resourceModel = $dispatcher->loadResourceModel($resourceClass, 'rest', $this->getUserType(), $this->getVersion());
            if ($resourceModel instanceof Mage_Api2_Model_Resource) {
                $reflectionObject = new ReflectionObject($resourceModel);
                $method = null;
                switch ($routeAction) {
                    case self::ACTION_TYPE_ENTITY:
                        $method = '_retrieve';
                        break;
                    case self::ACTION_TYPE_COLLECTION:
                        $method = '_retrieveCollection';
                        break;
                    default:
                        break;
                }
                if ($method) {
                    $getEntityMethod = $reflectionObject->getMethod($method);
                    $doc = $getEntityMethod->getDocComment();
                    $doc = nl2br(preg_replace('/^(\s*\/\*\*)|^(\s*\*)/mi', '', $doc));
                }
            }
        }
        return sprintf("%s:%s", $resourceType, $doc);
    }

    /**
     * Gets lest of resources with detailed info about each resource.
     * It's mainly used for help index
     *
     * @return Varien_Simplexml_Element
     */
    public function getDetailedResourceList()
    {
        $resources = $this->getConfig()->getNode('resources');
        return $resources;
    }

    /**
     * Dispatches API request
     *
     * @return void
     */
    public function dispatch()
    {
        switch ($this->getActionType() . $this->getOperation()) {
            case self::ACTION_TYPE_INFO . self::OPERATION_RETRIEVE:
                $this->_errorIfMethodNotExist('_getResourceInfo');
                $retrievedData = $this->_getResourceInfo();
                $this->_render($retrievedData);
                return;
            /**
             * Override update
             */
            case self::ACTION_TYPE_ENTITY . self::OPERATION_UPDATE:
                $this->_errorIfMethodNotExist('_update');
                $requestData = $this->getRequest()->getBodyParams();
                if (empty($requestData)) {
                    $this->_critical(self::RESOURCE_REQUEST_DATA_INVALID);
                }
                $filteredData = $this->getFilter()->in($requestData);
                if (empty($filteredData)) {
                    $this->_critical(self::RESOURCE_REQUEST_DATA_INVALID);
                }
                $this->_update($filteredData);
                $this->_render($this->getResponse()->getMessages());
                $responseCode = 200;
                foreach ($this->getResponse()->getMessages() as $msg => $message) {
                    if ($msg != 'success') {
                        $responseCode = $message[0]['code'];
                        break;
                    } else {
                        $responseCode = $message[0]['code'];
                    }
                }
                $this->getResponse()->setHttpResponseCode($responseCode);
                break;
            default:
                parent::dispatch();
        }
        return;
    }

    /**
     * Log helper for all api2 resources
     *
     * @param mixed $object
     *
     * @return mixed
     */
    protected function log($object)
    {
        Mage::helper('mageflow_connect/log')->log($object);
    }

    /**
     * Return collection of items. The type of items is defined by
     * workingModel and _resourceType
     *
     * @return array
     */
    public function _retrieveCollection()
    {
        $this->log('Start: ' . __METHOD__);
        $itemCollection = $this->getWorkingModel()->getCollection();

        $out = $this->packModelCollection($itemCollection);
        $this->log('Finish: ' . __METHOD__);
        return $out;
    }

    /**
     * Returns array of entity fields where attribute names are mapped to
     * actual entity fields
     *
     * @return array
     */
    public function getEntityFields()
    {
        if (empty($this->_entityFields)) {
            $node = $this->getConfig()->getNode(
                'resources/' . $this->_resourceType . '/attributes'
            );
            /**
             * @var Varien_Simplexml_Element $child
             */
            foreach ($node->children() as $child) {
                $entityField = $child->getName();
                if (trim($child->getAttribute('entity_field')) != '') {
                    $entityField = trim($child->getAttribute('entity_field'));
                }
                $this->_entityFields[$child->getName()] = $entityField;
            }
        }
        return $this->_entityFields;
    }

    /**
     * Handles create (POST) request for cms/block
     *
     * @param array $filteredData
     *
     * @return array|string
     */
    public function _create(array $filteredData)
    {
        $this->log('Start: ' . __METHOD__);

        $this->cleanCache();

        //we shouldn't have any original data in case of creation
        $originalData = null;


        Mage::dispatchEvent('before_receive_entity', array('original_data' => $filteredData));

        $out = $this->getDataProcessor()->processData($filteredData);

        if ($out['status'] == 'error') {
            $this->_errorMessage(sprintf('Could not save %s. Reason: ' . $out['message'], $this->getResourceType()), 500, $out);
            Mage::dispatchEvent('failed_receive_entity', array('original_data' => $filteredData));
            return array();
        }

        Mage::dispatchEvent('after_receive_entity', array('original_data' => $filteredData, 'data' => $out));

        $type = $this->getTypeHelper()->getType($this->getResourceType());
        $out['type'] = $type->short;


        // send overwritten data to mageflow
        if (isset($out['recovery_mf_guid'])) {
            $message = sprintf(
                'Successfully updated %s', $this->getResourceType()
            );
        } else {
            $message = sprintf(
                'Successfully created %s', $this->getResourceType()
            );
        }

        if (!empty($out['message'])) {
            $message = sprintf('%s. %s', $message, $out['message']);
        }

        $this->_successMessage($message, 200, $out);
        $this->log('Finish: ' . __METHOD__);

        return $out;
    }

    /**
     * add metadata to response array
     *
     * @param array $data
     *
     * @return array
     */
    protected function prepareResponse($data = array())
    {
        $responseMeta = array(
            'response_meta' => array(
                'timestamp' => time(),
                'item_count' => sizeof($data['items'])
            )
        );
        $out = array_merge($responseMeta, $data);
        $this->log($out);
        return $out;
    }

    /**
     * json encode $data and put into response body
     * add content type header to response
     *
     * @param array $data
     *
     * @return array
     */
    protected function sendJsonResponse(array $data)
    {
        return $data;

    }

    /**
     * prepares API client
     *
     * @return \Mageflow\Connect\Model\Api\Mageflow\Client
     */
    public function getApiClient()
    {
        /**
         * @var Mageflow_Connect_Helper_Oauth $helper
         */
        $helper = Mage::helper('mageflow_connect/oauth');
        $client = $helper->getApiClient();
        return $client;
    }

    /**
     * send overwritten data to MF
     *
     * @param       $type
     * @param array $filteredData
     * @param       $originalData
     * @return array
     */
    public function sendRollback($type, $filteredData, $originalData)
    {
        if (!isset($filteredData['deploymentpackage'])) {
            return array('rollback response' => 'no rollback target given');
        }

        $changesetItem = Mage::helper('mageflow_connect/changeset')->createChangesetFromItem($type, $originalData);

        $dataItem = array(
            'type' => str_replace(
                array('::', ':'),
                '/',
                $changesetItem->getType()
            ),
            'content' => $changesetItem->getContent(),
            'encoding' => $changesetItem->getEncoding(),
        );

        if ($changesetItem->getMetainfo()) {
            $dataItem['metainfo'] = $changesetItem->getMetainfo();
        } else {
            $dataItem['metainfo'] = array();
        }

        $company = Mage::app()->getStore()->getConfig(
            \Mageflow_Connect_Model_System_Config::API_COMPANY
        );
        $data = array(
            'company' => $company,
            'deploymentpackage' => $filteredData['deploymentpackage'],
            'items' => json_encode(array($dataItem)),
        );

        $client = $this->getApiClient();
        $response = $client->post('rollback', $data);

        return array('rollback response' => $response);
    }

    /**
     * Mimic multicreate because Magento API is a bit weird about it:)
     *
     * @param array $filteredData
     */
    public function _multiCreate(array $filteredData)
    {
        $this->log(__METHOD__);
        $out = array();
        foreach ($filteredData as $data) {
            if (!isset($data['mf_guid'])) {
                $data['mf_guid'] = null;
            }
            $out[] = $this->_create($data);
        }
        $this->log('OK');
    }

    /**
     * Maps output types
     *
     * @param $inArr
     * @param object $context
     * @return array
     */
    protected function mapOutputTypes($inArr, $context = null)
    {
        $xml = new SimpleXMLElement($this->getConfig()->getXmlString());
        $out = array();
        foreach ($inArr as $attributeName => $attributeValue) {
            $xpathStr = '//*/resources/' . $this->_resourceType . '/attributes/' . $attributeName . '|//*/resources/' . $this->_resourceType . '/attributes/*[@entity_field="' . $attributeName . '"]';
            $nodeArr = $xml->xpath($xpathStr);
            if (isset($nodeArr[0])) {
                $node = $nodeArr[0];
                $typeNode = $node->xpath('@type');
                $typeName = (string)$typeNode[0];
                /**
                 * @var Mageflow_Connect_Model_Mapper_Base $mapperObject
                 */
                $mapperObject = $this->getMapper($typeName);
                if ($mapperObject instanceof Mageflow_Connect_Model_Mapper_Base) {
                    $out[$attributeName] = $mapperObject->mapValue($attributeValue, $context);
                }
            }
        }
        return $out;
    }

    /**
     * Returns data type mapper for specified type
     *
     * @param $type
     * @return Mage_Core_Model_Abstract
     */
    protected function getMapper($type)
    {
        $mapper = null;
        switch ($type) {
            case 'store_code':
                $mapper = Mage::getModel('mageflow_connect/mapper_store_code');
                break;
            case 'website_code':
                $mapper = Mage::getModel('mageflow_connect/mapper_website_code');
                break;
            case 'storegroup_code':
                $mapper = Mage::getModel('mageflow_connect/mapper_storegroup_code');
                break;
            case 'url':
                $mapper = Mage::getModel('mageflow_connect/mapper_url');
                break;
            case 'mfguid':
                $mapper = Mage::getModel('mageflow_connect/mapper_mfguid');
                break;
            default:
                $mapper = Mage::getModel('mageflow_connect/mapper_base');
                break;
        }
        return $mapper;
    }

    /**
     * multi update
     *
     * @param array $filteredData
     */
    public function _multiUpdate(array $filteredData)
    {
        $this->log(__METHOD__);
        foreach ($filteredData as $data) {
            $this->_update($data);
        }
    }

    /**
     * update
     *
     * @param array $filteredData
     *
     * @return array|string
     */
    public function _update(array $filteredData)
    {
        $this->log(sprintf('%s', $filteredData));
        return $this->_create($filteredData);
    }

    /**
     * @param null $model
     * @return Mageflow_Connect_Model_Interfaces_Dataprocessor
     */
    protected function getDataProcessor($model = null)
    {
        $handlerId = $model;
        if (null === $model) {
            $handlerId = $this->getResourceType();
        }
        if (is_object($model)) {
            $handlerId = get_class($model);
        }
        $processorClass = $this->getTypeHelper()->getHandlerClass($handlerId);

        if ($processorClass != '') {
            $processor = Mage::getModel($processorClass, array('modelInstance' => $model));
        } else {
            $processor = Mage::getModel('mageflow_connect/handler_generic', array('modelInstance' => $model));
        }
        return $processor;
    }

    /**
     * @return Mageflow_Connect_Helper_Type
     */
    protected function getTypeHelper()
    {
        return Mage::helper('mageflow_connect/type');
    }

    /**
     * Cleans cache. This is required prior and after some operations
     * Cache is automatically cleaned only when configured so.
     */
    protected function cleanCache()
    {
        if (Mage::app()->getStore()->getConfig(Mageflow_Connect_Model_System_Config::AUTO_CLEAN_CACHE)) {
            /**
             * @var Mageflow_Connect_Helper_System $systemHelper
             */
            $systemHelper = Mage::helper('mageflow_connect/system');
            $systemHelper->cleanCache();
        }
    }

    /**
     * Packs data using common dataprocessor
     *
     * @param Mage_Core_Model_Abstract $model
     * @return null|stdClass
     */
    public function packModel($model)
    {
        $packer = $this->getDataProcessor();
        $c = null;
        if ($packer instanceof Mageflow_Connect_Model_Interfaces_Dataprocessor) {
            $c = $packer->packData($model);
        }
        return $c;
    }

    /**
     * Helper method for packing model collection
     *
     * @param $collection
     * @return array
     */
    public function packModelCollection($collection)
    {
        $out = array();
        /**
         * @var Mage_Catalog_Model_Category $item
         */
        foreach ($collection->getItems() as $item) {
            $c = $this->packModel($item);
            if (!is_null($c)) {
                $out[] = $c;
            }
        }
        return $out;
    }

    /**
     * delete entity
     *
     * @return array|void
     * @throws Exception
     */
    public function _delete()
    {
        $out = array('messages' => array());

        $resourceModel = $this->getWorkingModel();
        $itemCollection = $resourceModel->getCollection();
        $mf_guid = $this->getRequest()->getParam('mf_guid', null);
        if (!is_null($mf_guid)) {

            $itemCollection->addFieldToFilter(array('mf_guid'),
                array(
                    array(
                        'eq' => $mf_guid
                    ),
                )
            );

            foreach ($itemCollection as $item) {
                $itemTitle = $item->getTitle();
                $item->delete();
                $out['messages'][] = 'Deleted poll ' . $itemTitle;
            }
        }

        $this->log($out);
        return $out;
    }
}
