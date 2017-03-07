<?php
require_once 'Mageflow/Connect/Module.php';

/**
 * Oauth.php
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
 * Mageflow_Connect_Helper_Oauth
 * MageFlow OAuth helper that deals with setting up Magento OAuth consumer
 * as well as returning MageFlow API client instance
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Helper_Oauth extends Mageflow_Connect_Helper_Data
{

    /**
     * Initialized Mageflow module that ensures loading libs and deps
     */
    public function __construct()
    {
        $m = new \Mageflow\Connect\Module();
    }

    /**
     * Helper method to create OAuth consumer
     *
     * @param $instanceKey
     *
     * @return mixed|stdClass
     */
    public function createOAuthConsumer($instanceKey)
    {
        $response = new stdClass();
        $response->success = false;
        try {
            /**
             * @var Mage_Oauth_Model_Consumer $oauthConsumerModel
             */
            $oauthConsumerModel = $this->createConsumerModel($instanceKey);

            if ($oauthConsumerModel->getId() > 0) {

                $token = $this->createToken($oauthConsumerModel);

                //send registraton info and keys to MageFlow HERE
                $findClient = $this->getApiClient();
                $findRequest = 'find/Instance/instance_key/' . $instanceKey;
                $this->log(
                    'Searching for existing entity: ' . $findRequest
                );

                $findResponse = $findClient->get($findRequest);

                $instanceData = json_decode($findResponse);

                $this->log(
                    print_r($instanceData, true)
                );

                $instanceId = $instanceData->items[0]->id;

                if ($instanceId < 1) {
                    $this->log(
                        'ERROR: Could not fetch
                        instance ID and cannot continue without it.'
                    );
                    $response->success = false;
                    $response->errrorMessage = "Could not retrieve instance ID";

                    return $response;
                }

                $data = array(
                    'consumer_key' => $oauthConsumerModel->getKey(),
                    'consumer_secret' => $oauthConsumerModel->getSecret(),
                    'token' => $token->getToken(),
                    'token_secret' => $token->getSecret()
                );

                $client = $this->getApiClient();

                $this->log(
                    'Registering OAuth consumer at MageFlow'
                );

                $encodedResponse = $client->put(
                    'access/' . $instanceKey,
                    $data
                );

                $response = json_decode($encodedResponse);

                $this->log(
                    'Response: ' . print_r($response, true)
                );

                if (!empty($response)) {
                    $response->success = true;
                }
            }
        } catch (Exception $e) {
            $this->log($e->getMessage());
            $response->success
                = false;
            $response->errormessage
                = $e->getMessage();
        }

        return $response;
    }

    /**
     * Creates oauth consumer model
     *
     * @param $instanceKey
     *
     * @return object
     */
    public function createConsumerModel($instanceKey)
    {
        $adminUserName = $instanceKey . '_oauth';
        $this->log($adminUserName);

        $adminUserModel = Mage::getModel('admin/user');
        $adminUserModel->loadByUsername($adminUserName);
        if ($adminUserModel->getId() <= 0) {
            $adminUserModel->setEmail(
                $adminUserName . '@oauth.mageflow.com'
            );
            $adminUserModel->setUsername($adminUserName);
            $adminUserModel->setFirstname('Mageflow');
            $adminUserModel->setLastname('Consumer');
            $password = Mage::helper('mageflow_connect')->randomHash();
            $adminUserModel->setPassword($password);
            $adminUserModel->save();

            $rootRoleModel = Mage::getModel('admin/role')->getCollection()
                ->addFilter('role_type', 'G')->addFilter('tree_level', 1)
                ->getFirstItem();


            $adminRoleModel = Mage::getModel('admin/role');
            $adminRoleModel->setUserId($adminUserModel->getId());
            $adminRoleModel->setParentId($rootRoleModel->getId());
            $adminRoleModel->setRoleType('U');
            $adminRoleModel->setTreeLevel(2);
            $adminRoleModel->setRoleName($adminUserModel->getUsername());
            $adminRoleModel->save();

        }
        //set API2 user role
        //add creation of admin role of it does not exist
        $apiAclRole = Mage::getModel('api2/acl_global_role')->getCollection()->addFilter('role_name', 'Admin')
            ->getFirstItem();

        if (!($apiAclRole instanceof Mage_Api2_Model_Acl_Global_Role)
            || !$apiAclRole->getId()
        ) {
            $apiAclRole->setRoleName('Admin');
            $apiAclRole->save();
            /**
             * @var Mage_Api2_Model_Acl_Global_Rule $rule
             */
            $rule = Mage::getModel('api2/acl_global_rule');
            $collection = $rule->getCollection();
            $ruleItem = $collection->addFilterByRoleId($apiAclRole->getId())
                ->getFirstItem();
            $ruleItem->setRoleId($apiAclRole->getId());
            $ruleItem->setResourceId(
                Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL
            );
            $ruleItem->save();
        }

        //save admin user to role relation
        Mage::getModel('api2/acl_global_role')
            ->getResource()->saveAdminToRoleRelation(
                $adminUserModel->getId(),
                $apiAclRole->getId()
            );


        $apiAclAttribute = Mage::getModel('api2/acl_filter_attribute')
            ->getCollection()
            ->addFilter('user_type', 'admin')->getFirstItem();
        if (!($apiAclAttribute instanceof
                Mage_Api2_Model_Acl_Filter_Attribute)
            || !$apiAclAttribute->getId()
        ) {
            $apiAclAttribute->setUserType('admin');
            $apiAclAttribute->setResourceId(
                Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL
            );
            $apiAclAttribute->save();
        }
        $oauthConsumerModel = Mage::getModel('oauth/consumer');
        //create admin user with the same username
        $oauthConsumerModel->load($adminUserName, 'name');
        if ($adminUserModel->getId() > 0
            && $oauthConsumerModel->getId() <= 0
        ) {
            $oauthConsumerModel->setName($adminUserName);
            $oauthConsumerModel->setKey(
                Mage::helper('mageflow_connect')->randomHash(32)
            );
            $oauthConsumerModel->setSecret(
                Mage::helper('mageflow_connect')->randomHash(32)
            );
            $oauthConsumerModel->save();
            $oauthConsumerId = $oauthConsumerModel->getId();
            $this->log(
                'Created OAuth consumer with ID ' . $oauthConsumerId
            );
        }
        $oauthConsumerModel->setAdminUserModel($adminUserModel);

        return $oauthConsumerModel;
    }

    /**
     * Creates access token
     *
     * @param Mage_Oauth_Model_Consumer $oauthConsumerModel
     *
     * @return Mage_Oauth_Model_Token
     */
    public function createToken($oauthConsumerModel)
    {
        $token = Mage::getModel('oauth/token');

        $token->createRequestToken(
            $oauthConsumerModel->getId(),
            'http://escape.to.the.void/' . Mage::helper('mageflow_connect')
                ->randomHash(16) . '/'
        );

        $token->authorize(
            $oauthConsumerModel->getAdminUserModel()->getId(),
            Mage_Oauth_Model_Token::USER_TYPE_ADMIN
        );

        $token->convertToAccess();

        return $token;
    }

    /**
     * Returns MageFlow API client instance
     *
     * @return \Mageflow\Connect\Model\Api\Mageflow\Client
     */
    public function getApiClient()
    {
        $this->log(
            'Creating and configuring MageFlow API client',
            __METHOD__,
            __LINE__
        );

        /**
         * @var Mage_Core_Helper_Data $coreHelper
         */
        $coreHelper = Mage::helper('core');

        $configuration = new stdClass();

        Mage::app()->getConfig()->cleanCache();

        $configuration->_consumerKey = $coreHelper->decrypt(
            Mage::app()->getStore()->getConfig(Mageflow_Connect_Model_System_Config::API_CONSUMER_KEY)
        );


        $configuration->_token = $coreHelper->decrypt(
            Mage::app()->getStore()->getConfig(Mageflow_Connect_Model_System_Config::API_TOKEN)
        );


//		$companyArr = unserialize(
//			\Mage::app()->getStore()->getConfig(
//				\Mageflow_Connect_Model_System_Config::API_COMPANY_NAME
//			)
//		);
//
//		$configuration->_company = $companyArr['id'];
//
//		$configuration->_project = \Mage::app()->getStore()->getConfig(
//			\Mageflow_Connect_Model_System_Config::API_PROJECT
//		);
//
//		$configuration->_instanceKey = \Mage::app()->getStore()
//		                                    ->getConfig(
//			                                    \Mageflow_Connect_Model_System_Config::API_INSTANCE_KEY
//		                                    );
//
//		$this->log(
//			$configuration,
//			__METHOD__,
//			__LINE__
//		);

        $configuration->_consumerSecret = $coreHelper->decrypt(
            Mage::app()->getStore()->getConfig(Mageflow_Connect_Model_System_Config::API_CONSUMER_SECRET)
        );
        $configuration->_tokenSecret = $coreHelper->decrypt(
            Mage::app()->getStore()->getConfig(Mageflow_Connect_Model_System_Config::API_TOKEN_SECRET)
        );

        $this->log(
            'Added secret keys to configuration: ',
            __METHOD__,
            __LINE__
        );

        $client = new \Mageflow\Connect\Model\Api\Mageflow\Client($configuration);


        return $client;
    }

    /**
     * Creates admin user
     *
     * @param      $email
     * @param      $firstname
     * @param      $lastname
     * @param      $password
     * @param bool $sendReset
     *
     * @return false|Mage_Core_Model_Abstract
     */
    public function createAdminUser($email, $firstname, $lastname, $password, $sendReset = false)
    {
        /**
         * @var \Mage_Admin_Model_User
         */
        $adminUser = Mage::getModel('admin/user');
        $adminUser = $adminUser->loadByUsername($email);
        if (!$adminUser->getId()) {
            $adminUser->setUsername($email);
            $adminUser->setEmail($email);
            $adminUser->setFirstname($firstname);
            $adminUser->setLastname($lastname);
            $adminUser->setPassword($password);
            $adminUser->save();
            $userId = $adminUser->getId();
            $adminRole = Mage::getModel('admin/role');
            $adminRole->setUserId($userId);
            $adminRole->setParentId(1);
            $adminRole->setRoleType('U');
            $adminRole->setTreeLevel(2);
            $adminRole->setRoleName(ucfirst($adminUser->getUsername()));
            $adminRole->save();

        }
        $newResetPasswordLinkToken = null;
        if ($sendReset) {
            $newResetPasswordLinkToken = Mage::helper('admin')
                ->generateResetPasswordLinkToken();
            $adminUser->changeResetPasswordLinkToken($newResetPasswordLinkToken);
            $adminUser->save();
        }

        return $adminUser;
    }

    /**
     * Returns Oauth configuration
     *
     * @return array
     */
    public function getConfig()
    {

        $baseUrl = Mage::app()->getStore()->getConfig(
            Mageflow_Connect_Model_System_Config::API_URL
        );

        $consumerKey = Mage::helper('core')->decrypt(Mage::app()->getStore()->getConfig(
            Mageflow_Connect_Model_System_Config::API_CONSUMER_KEY
        ));

        $consumerSecret = Mage::helper('core')->decrypt(Mage::app()->getStore()->getConfig(
            Mageflow_Connect_Model_System_Config::API_CONSUMER_SECRET
        ));

        $token = Mage::helper('core')->decrypt(Mage::app()->getStore()->getConfig(
            Mageflow_Connect_Model_System_Config::API_TOKEN
        ));
        $tokenSecret = Mage::helper('core')->decrypt(Mage::app()->getStore()->getConfig(
            Mageflow_Connect_Model_System_Config::API_TOKEN_SECRET
        ));

        $config = array(
            'baseUrl' => $baseUrl,
            'requestTokenUrl' => $baseUrl . 'oauth/initiate',
            'accessTokenUrl' => $baseUrl . 'oauth/token',
            'authorizeUrl' => $baseUrl . 'admin/oauth_authorize',
            'callbackUrl' => $url = \Mage::helper('adminhtml')->getUrl('adminhtml/oauth_callback'),
            'consumerKey' => $consumerKey,
            'consumerSecret' => $consumerSecret,
            'token' => $token,
            'tokenSecret' => $tokenSecret,
        );

        return $config;
    }

    /**
     * Returns HTTP client from accessToken
     *
     * @return Zend_Oauth_Client
     */
    public function getClientFromAccessToken()
    {
        $config = $this->getConfig();

        /**
         * @var $token Zend_Oauth_Token_Access
         *
         * NB! Token can be and should be loaded from elsewhere than session.
         * It should be stored in database
         */
        $token = new Zend_Oauth_Token_Access();
        $token->setToken($config['token']);
        $token->setTokenSecret($config['tokenSecret']);
        $client = $token->getHttpClient(
            array(
                'consumerKey' => $config['consumerKey'],
                'consumerSecret' => $config['consumerSecret']
            )
        );
        $client->setHeaders(
            array(
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            )
        );

        $contextParams = array(
            'http' => array(
                'timeout' => Mageflow_Connect_Oauth_CallbackController::REQUEST_TIMEOUT
            ),
        );

        $adapter = new Zend_Http_Client_Adapter_Socket();
        $adapter->setStreamContext(
            stream_context_create($contextParams)
        );

        $client->setAdapter($adapter);

        return $client;
    }
}