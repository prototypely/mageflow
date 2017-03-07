<?php


include_once 'AbstractTest.php';

/**
 *
 * Product.php
 *
 * Tests product resource related aspects
 *
 * @author sven
 * @created 07/18/2014 11:51
 */
class Mageflow_Connect_Website extends Mageflow_Connect_AbstractTest
{

    protected $resourceBase = 'system/website';


    /**
     * @codeCoverageIgnore
     */
    public function testWebsites()
    {
        printf("Testing /%s resource\n", $this->resourceBase);
        $url = sprintf('%s/%s', $this->baseUrl, $this->resourceBase);
        printf("Url: %s\n", $url);
        $retval = $this->getApiClient()->fetch($url, array(), OAUTH_HTTP_METHOD_GET, $this->headers);
        $json = $this->getApiClient()->getLastResponse();
        $this->assertTrue($retval);
        $responseObject = json_decode($json);
        $this->assertTrue(is_array($responseObject));
        $this->assertTrue(count($responseObject) > 0);

        foreach($responseObject as $website){
            $this->assertObjectHasAttribute('mf_guid', $website, 'Website does not have MFGUID');
        }
    }

}
 