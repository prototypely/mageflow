<?php
include_once 'AbstractTest.php';


/**
 *
 * Apitest.php
 *
 * @author peeter
 * @created 
 */
class TestPromotionRuleCatalog extends Mageflow_Connect_AbstractTest
{


    /**
     * POSTs new entity called...
     */
    public function testPostPromotionRuleCatalog()
    {
        $json = $this->testData->getPromotionRuleCatalogForPost();
        $resource = 'promotion/rule/catalog';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->postJson($url, $json);
    }
}
