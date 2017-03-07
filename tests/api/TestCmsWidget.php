<?php
include_once 'AbstractTest.php';


/**
 *
 * Apitest.php
 *
 * @author peeter
 * @created 
 */
class TestCmsWidget extends Mageflow_Connect_AbstractTest
{


    /**
     * POSTs new CMS Widget
     */
    public function testPostCmsWidget()
    {
        $json = $this->testData->getCMSWidgetForPost();
        $resource = 'cms/widget';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->postJson($url, $json);
    }

/**
     * GETs CMS Widget. Made by Peeter on December 17th, 2014
     */
    public function testGetCmsWidget()
    {
        
        $resource = 'cms/widget';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->getJson($url);
    }







}
