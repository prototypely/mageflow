<?php
include_once 'AbstractTest.php';

/**
 *
 * Testconfig.php
 *
 * @author sven
 * @created 11/17/2014 10:29
 */
class Testconfig extends Mageflow_Connect_AbstractTest
{

    public function setUp()
    {
        parent::setUp();
    }

    public function testDumpConfig()
    {
        $config = Mage::getConfig()->loadModulesConfiguration('system.xml')->applyExtends();
        $xml = $config->getXmlString();
        $dom = new DOMDocument('1.0');
        $dom->formatOutput = true;
        $dom->loadXML($xml);
//        print_r($dom->saveXML());
        $domNodeList = $dom->getElementsByTagName('groups');

        foreach ($domNodeList as $domNode) {
            print_r($domNode);
        }
    }
} 