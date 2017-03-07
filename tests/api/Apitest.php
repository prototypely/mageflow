<?php
include_once 'AbstractTest.php';
include_once 'Testdata.php';


/**
 *
 * Apitest.php
 *
 * @author  sven
 * @created 07/18/2014 11:51
 */
class Mageflow_Connect_Apitest extends Mageflow_Connect_AbstractTest
{

    protected $resourceList
        = array(
            'GET'  => array(
                'system/info' => array(
                    'total_memory'
                ),
            ),
            'POST' => array(
                'cms/page'             => array(
                    'mf_guid',
                ),
                'system/role'          => array(
                    'role_name'
                ),
                'system/user'          => array(
                    'mf_guid'
                ),
                'cms/block'            => array(
                    'mf_guid'
                ),
                'catalog/category'     => array(
                    'mf_guid'
                ),
                'catalog/attribute'    => array(
                    'attribute_code'
                ),
                'catalog/attributeset' => array(
                    'mf_guid'
                ),
                'system/configuration' => array(
                    'path'
                ),
                'email/template'       => array(
                    'mf_guid'
                )
            ),
            'PUT'  => array(
                'catalog/product' => array(
                    'sku'
                ),

            )
        );


    /**
     * @codeCoverageIgnore
     */
    public function testHelpResource()
    {
//        $this->markTestSkipped();
        $resource = 'help';
        printf("Testing /%s resource\n", $resource);
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        printf("Url: %s\n", $url);
        $retval = $this->getApiClient()->fetch($url, array(), OAUTH_HTTP_METHOD_GET, $this->headers);
        $json = $this->getApiClient()->getLastResponse();
        $this->assertTrue($retval);
        $responseObject = json_decode($json);
        $this->assertTrue(is_object($responseObject));
        $this->assertTrue(is_array($responseObject->resources));
        $this->assertTrue(count($responseObject->resources) > 0);
    }

    /**
     * Test POST resource
     *
     * POST should occur before GET because otherwise GET does not have anything to GET in case of a clean Magento
     *
     * @deprecated
     */
    public function testPostResources()
    {
        if (getenv('skip_POST')) {
            $this->markTestSkipped('Skipped in configuration');
            return;
        }
        $method = 'POST';
        printf("\nTesting %s", $method);
        foreach ($this->resourceList['POST'] as $resource => $requiredFields) {
            $url = sprintf('%s/%s', $this->baseUrl, $resource);

            printf("\n%s", $resource);

            ob_flush();

            /**
             * 1. get testdata with randomized important keys
             * 2. get randomly an object from the list of results
             */

            $json = $this->testData->getTestObjectForPost($resource);

            $responseJson = $this->postJson($url, $json);
            $responseObject = json_decode($responseJson);
            if (is_object($responseObject) && is_array($responseObject->success)) {
                $currentEntity = $responseObject->success[0]->current_entity;
                $mfGuid = $currentEntity->mf_guid;
                if ($mfGuid) {
                    $url = $url . '/' . $mfGuid;
                    print_r($url);
                    $retval = $this->getApiClient()->fetch($url, array(), OAUTH_HTTP_METHOD_GET, $this->headers);
                    $responseJson = $this->getApiClient()->getLastResponse();
                    $newResponseObject = json_decode($responseJson);
                    print_r($newResponseObject);
                } else {
                    throw new Exception('mf_guid not found in response object');
                }

            } else {
                throw new Exception('Response not an object nor an array');
            }
//            try {
//                printf("\nPosting\n %s\n to\n %s", $json, $url);
//                $retval = $this->getApiClient()->fetch($url, $json, OAUTH_HTTP_METHOD_POST, $this->headers);
//                printf("\nResponse:\n%s\n", print_r($this->getApiClient()->getLastResponseInfo(), true));
//            } catch (\Exception $ex) {
//                printf("\nException:\n%s\n", $ex->getMessage());
////                printf($ex->getTraceAsString());
//            }
//
//            printf("\nReturned: %s\n", $retval);
//            $responseJson = $this->getApiClient()->getLastResponse();
//            $response = json_decode($responseJson);
//            $this->assertNotNull($response->success);
//            if ($response->success) {
//                print_r($response->success[0]->message);
//            }
        }
    }

    /**
     * Test GET resources
     */
    public function testGetResources()
    {
        $this->markTestSkipped('skipped');
        $method = 'GET';
        printf("\nTesting %s", $method);
        $resourceList = array_merge(
            $this->resourceList['GET'], $this->resourceList['PUT'], $this->resourceList['POST']
        );
        foreach ($resourceList as $resource => $requiredFields) {
            $url = sprintf('%s/%s', $this->baseUrl, $resource);

            printf("\n%s", $resource);
            ob_flush();

            $retval = $this->getApiClient()->fetch($url, array(), OAUTH_HTTP_METHOD_GET, $this->headers);
            $this->assertTrue($retval);

            $json = $this->getApiClient()->getLastResponse();

            $this->assertNotNull($json);

            $objectArray = json_decode($json);

            $this->assertTrue(is_array($objectArray));
            $this->assertTrue(count($objectArray) > 0);
            foreach ($objectArray as $object) {
                foreach ($requiredFields as $fieldName) {
                    $this->assertObjectHasAttribute(
                        $fieldName, $object,
                        sprintf("Field %s not found in object \n%s\n", $fieldName, print_r($object, true))
                    );
                }
            }
            printf("\t\t\tOK");
        }
    }

    /**
     * Test PUT resource
     */
    public function testPutResources()
    {
        $this->markTestSkipped('Skipped in configuration');
        if (getenv('skip_PUT')) {
            $this->markTestSkipped('Skipped in configuration');
            return;
        }
        $method = 'PUT';
        printf("\nTesting %s", $method);
        foreach ($this->resourceList['POST'] as $resource => $requiredFields) {
            $url = sprintf('%s/%s', $this->baseUrl, $resource);

            printf("\n%s", $resource);

            ob_flush();

            $this->setExpectedException('OAuthException');

            /**
             * 1. get results from a resource
             * 2. get randomly an object from the list of results
             * 3. PUT it back to resource
             */
            $retval = $this->getApiClient()->fetch($url, array(), OAUTH_HTTP_METHOD_GET, $this->headers);
            $this->assertTrue($retval);

            $json = $this->getApiClient()->getLastResponse();

            $objectList = json_decode($json);

            $this->assertTrue(is_array($objectList));

            $randomKey = array_rand($objectList);

            $object = $objectList[$randomKey];

            $this->assertTrue(is_object($object));

            foreach ($requiredFields as $keyFieldName) {
                $keyFieldValue = $object->$keyFieldName;
                $url .= '/' . rawurlencode(str_replace('/', ':', $keyFieldValue));
            }
            echo "\n" . $url . "\n";

            $retval = $this->getApiClient()->fetch($url, array(), OAUTH_HTTP_METHOD_GET, $this->headers);
            $this->assertTrue($retval);

            $json = $this->getApiClient()->getLastResponse();
            $this->assertNotNull($json);

            $objectList = json_decode($json);
            $this->assertTrue(is_array($objectList), "Resultset is not an array");
            $this->assertTrue(sizeof($objectList) == 1, "Resultset contains more than 1 item");

            $json = json_encode($objectList);

            $this->getApiClient()->enableDebug();

            try {
                $retval = $this->getApiClient()->fetch($url, $json, OAUTH_HTTP_METHOD_PUT, $this->headers);
            } catch (\Exception $ex) {
                printf($ex->getMessage());
                printf($ex->getTraceAsString());
            }

            printf("\nReturned: %s\n", $retval);
//            print_r($this->getApiClient()->getLastResponseInfo());
            $responseJson = $this->getApiClient()->getLastResponse();
            $response = json_decode($responseJson);
            $this->assertNotNull($response->success);
            if ($response->success) {
                print_r($response->success[0]->message);
            }
            echo "\n";
        }
    }


}
 