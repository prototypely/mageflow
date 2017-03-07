<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class XmlfilesTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function testXmlFiles()
    {
        $cmd = sprintf('/bin/find %s -name "*.xml" | xargs /usr/bin/xmllint', realpath(__DIR__.'/../../public/app/code/community'));
        $retval = exec($cmd);
        $this->assertNotContains('not well', $retval);
    }

}
