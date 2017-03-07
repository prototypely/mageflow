
<?php
include_once 'AbstractTest.php';


/**
 *
 * Apitest.php
 *
 * @author peeter
 * @created 
 */
class TestCustomerGroup extends Mageflow_Connect_AbstractTest
{


    /**
     * POSTs entity called customer/group.
     */
    public function testPostCustomerGroup()
    {
        $json = $this->testData->getCustomerGroupForPost();
        $resource = 'customer/group';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->postJson($url, $json);
    }

/**
     * GETs entity called customer/group. Made by Peeter on December 17th, 2014
     */
    public function testGetCustomerGroup()
    {
        
        $resource = 'customer/group';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->getJson($url);
    }



    
}
