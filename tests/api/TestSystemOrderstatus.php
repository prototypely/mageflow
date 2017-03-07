<?php
include_once 'AbstractTest.php';


/**
 *
 * Apitest.php
 *
 * @author peeter
 * @created 
 */
class TestSystemOrderStatus extends Mageflow_Connect_AbstractTest
{


    /**
     * POSTs new system order status
     */
    public function testPostSystemOrderStatus()
    {
        $json = $this->testData->getSystemOrderStatusForPost();
        $resource = 'system/orderstatus';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->postJson($url, $json);
    }



/**
     * GETs system/orderstatus. Made by Peeter on December 17th, 2014
     */
    public function testGetSystemOrderstatus()
    {
        
        $resource = 'system/orderstatus';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->getJson($url);
    }
}
