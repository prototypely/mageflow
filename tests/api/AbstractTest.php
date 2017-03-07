<?php
include_once 'Testdata.php';

/**
 *
 * AbstractTest.php
 *
 * @author  sven
 * @created 07/31/2014 14:20
 */
abstract class Mageflow_Connect_AbstractTest extends PHPUnit_Framework_TestCase
{

    protected $apiClient = null;
    protected $consumerKey;
    protected $consumerSecret;
    protected $token;
    protected $tokenSecret;
    protected $baseUrl;
    protected $headers = array('Accept' => 'application/json', 'Content-Type' => 'application/json');
    protected $env = 'development';

    /**
     * @var Testdata
     */
    protected $testData;

    protected $resourceList
        = array(
            'GET'  => array(
                'system/info' => array(
                    'total_memory'
                ),
            ),
            'POST' => array(
                'cms/page'               => array(
                    'mf_guid',
                ),
                'system/role'            => array(
                    'role_name'
                ),
                'system/user'            => array(
                    'mf_guid'
                ),
                'cms/block'              => array(
                    'mf_guid'
                ),
                'catalog/category'       => array(
                    'mf_guid'
                ),
                'catalog/attribute'      => array(
                    'attribute_code'
                ),
                'catalog/attributeset'   => array(
                    'mf_guid'
                ),
                'catalog/attributegroup' => array(
                    'entity_type_id',
                    'attribute_group_name',
                    'attribute_set_id'
                ),
                'system/configuration'   => array(
                    'path'
                ),
                'email/template'         => array(
                    'mf_guid'
                )
            ),
            'PUT'  => array(
                'catalog/product' => array(
                    'sku'
                ),

            )
        );

    public function setUp()
    {
        parent::setUp();
        if (getenv('AUTO_SETUP_ENV')) {
            $this->autoSetupEnvironment();
        }
        if (false != getenv('APPLICATION_ENV')) {
            $this->env = getenv('APPLICATION_ENV');
            printf("\nSetting env to %s\n", $this->env);
        }
        if (null === $this->baseUrl) {
            $this->baseUrl = getenv('bamboo_base_url');
            $this->consumerKey = getenv('bamboo_consumer_key');
            $this->consumerSecret = getenv('bamboo_consumer_secret');
            $this->token = getenv('bamboo_token');
            $this->tokenSecret = getenv('bamboo_token_secret');
        }
        $this->testData = new Testdata();
    }

    /**
     * This method sets up base URL and oauth keys from local
     * database
     */
    private function autoSetupEnvironment()
    {
        $this->getOauthKeys();
        $this->setUpBaseUrl();
    }

    private function setUpBaseUrl()
    {
        $this->baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'api/rest';
    }

    private function getOauthKeys()
    {
        $tokenModelCollection = Mage::getModel('oauth/token')->getCollection();
        /**
         * @var Mage_Oauth_Model_Token $tokenModel
         */
        $tokenModel = $tokenModelCollection->getFirstItem();
        $this->consumerKey = $tokenModel->getConsumer()->getKey();
        $this->consumerSecret = $tokenModel->getConsumer()->getSecret();
        $this->token = $tokenModel->getToken();
        $this->tokenSecret = $tokenModel->getSecret();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @return \OAuth
     */
    protected function getApiClient()
    {
        if (is_null($this->apiClient)) {
            printf("\n");
            printf("Consumer key: %.12s ...\n", $this->consumerKey);
            printf("Consumer secret: %.12s ...\n", $this->consumerSecret);
            printf("Token: %.12s ...\n", $this->token);
            printf("Token secret: %.12s ...\n", $this->tokenSecret);
            $this->apiClient = new OAuth($this->consumerKey, $this->consumerSecret, OAUTH_SIG_METHOD_HMACSHA1);
            $this->apiClient->setToken($this->token, $this->tokenSecret);
        }
        return $this->apiClient;
    }

    /**
     * Post JSON to resource URL
     *
     * @param $url
     * @param $json
     */
    protected function postJson($url, $json)
    {
        try {
            $retval = $this->getApiClient()->fetch($url, $json, OAUTH_HTTP_METHOD_POST, $this->headers);
        } catch (Exception $ex) {
//            print_r($ex);
            $this->assertLessThanOrEqual(300, $ex->getCode());
        }
//        print_r($this->getApiClient()->getLastResponseInfo());
        $response = $this->getApiClient()->getLastResponse();
//        print_r($response);
        return $response;
    }




 /***This is an experimental function made by Peeter on December, 17th 2014
     * Get JSON to resource URL
     * @param $url
     * 
     */
    protected function getJson($url)
    {
       try {
            $retval = $this->getApiClient()->fetch($url, OAUTH_HTTP_METHOD_GET, $this->headers);
            $response = $this->getApiClient()->getLastResponseInfo();
        } catch (Exception $ex) {
            $this->assertLessThanOrEqual(300, $ex->getCode());
        }
    }










}