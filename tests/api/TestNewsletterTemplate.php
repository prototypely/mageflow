<?php
include_once 'AbstractTest.php';


/**
 *
 * Apitest.php
 *
 * @author peeter
 * @created 
 */
class TestNewsletterTemplate extends Mageflow_Connect_AbstractTest
{


    /**
     * POSTs new entity Newsletter Template
     */
    public function testPostNewsletterTemplate()
    {
        $json = $this->testData->getNewsletterTemplateForPost();
        $resource = 'newsletter/template';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->postJson($url, $json);
    }




/**
     * GETs Newsletter Template. Made by Peeter on December 17th, 2014
     */
    public function testGetNewsletterTemplate()
    {
        
        $resource = 'newsletter/template';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->getJson($url);
    }



}
