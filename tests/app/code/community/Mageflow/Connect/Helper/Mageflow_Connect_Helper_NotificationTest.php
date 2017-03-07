<?php

/**
 *
 * Mageflow_Connect_Helper_NotificationTest.php
 *
 * @author sven
 * @created 09/11/2014 09:16
 */
class Mageflow_Connect_Helper_NotificationTest extends PHPUnit_Framework_TestCase
{

    /* @var Mageflow_Connect_Helper_Notification $object */
    protected $object;

    public function setUp()
    {
        parent::setUp();
        $this->object = Mage::helper('mageflow_connect/notification');
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testPostNotification()
    {

        $inboxModel = $this->object->postNotification('blaah', 'daah');
        $this->assertInstanceOf('Mage_AdminNotification_Model_Inbox', $inboxModel);

    }
}
 