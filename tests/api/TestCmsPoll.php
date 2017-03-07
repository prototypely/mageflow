<?php
include_once 'AbstractTest.php';


/**
 *
 * Apitest.php
 *
 * @author peeter
 * @created 
 */
class TestCmsPoll extends Mageflow_Connect_AbstractTest
{


    /**
     * POSTs new CMS Poll
     */
    public function testPostCmsPoll()
    {
        $json = $this->testData->getCmsPollForPost();
        $resource = 'cms/poll';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->postJson($url, $json);
    }


/**
     * GETs CMS Poll. Made by Peeter on December 17th, 2014
     */
    public function testGetCmsPoll()
    {
        
        $resource = 'cms/poll';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->getJson($url);
    }






}
