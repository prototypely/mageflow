<?php
include_once 'AbstractTest.php';


/**
 *
 * Apitest.php
 *
 * @author peeter
 * @created 
 */
class TestSystemDesign extends Mageflow_Connect_AbstractTest
{


    /**
     * POSTs new System Design
     */
    public function testPostSystemDesign()
    {
        $json = $this->testData->getSystemDesignForPost();
        $resource = 'system/design';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->postJson($url, $json);
    }




/**
     * GETs new System Design. Made by Peeter on December 17th, 2014
     */
    public function testGetSystemDesign()
    {
        
        $resource = 'system/design';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->getJson($url);
    }









}
