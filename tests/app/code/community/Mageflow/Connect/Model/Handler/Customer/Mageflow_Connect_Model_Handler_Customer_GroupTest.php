<?php
/**
 * Created by PhpStorm.
 * User: urmas
 * Date: 12/4/14
 * Time: 5:15 PM
 */

class Mageflow_Connect_Model_Handler_Customer_GroupTest  extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mageflow_Connect_Model_Handler_System_Orderstatus
     */
    protected $object;
    protected $testmodel;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
//        $this->object =  Mage::getModel('Mage_Core_Model_Orderstatus');
        $this->object = new Mageflow_Connect_Model_Handler_System_Orderstatus();

        $this->testmodel = Mage::getModel('Mage_Customer_Model_Group');

        $now = new Zend_Date();

        $this->testmodel->setData('mf_guid', rand(111111, 999999));
        $this->testmodel->setData('customer_group_code', 'testing');
        $this->testmodel->setData('tax_class_id', '3');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->testmodel->delete();
    }

    /**
     * @covers Mageflow_Connect_Model_Handler_System_Orderstatus::packData
     *
     * public function packData(Mage_Core_Model_Abstract $model)
     */
    public function testPackData()
    {
        $retval = $this->object->packData($this->testmodel);

        $this->assertObjectHasAttribute('mf_guid', $retval);
        $this->assertObjectHasAttribute('customer_group_code', $retval);
        $this->assertObjectHasAttribute('tax_class_id', $retval);
    }

    /**
     * @covers Mageflow_Connect_Model_Handler_System_Orderstatus::getPreview
     *
     */
    public function testGetPreview()
    {

        $this->assertTrue(method_exists($this->object, 'getPreview'));
        $changesetItem = Mage::getModel('mageflow_connect/changeset_item');
        $encodedContent = json_encode(
            $this->object->packData($this->testmodel),
            JSON_FORCE_OBJECT
        );
        $changesetItem->setContent($encodedContent);

        $retval = $this->object->getPreview($changesetItem);

        $this->assertEquals($retval, 'testing');
    }

    /**
     * @covers Mageflow_Connect_Model_Handler_System_Orderstatus::processData
     *
     * public function processData(array $data = array())
     */
    public function testProcessData()
    {
        $this->assertTrue(method_exists($this->object, 'processData'));
        $retval = $this->object->processData($this->testmodel->getData());

        $this->assertArrayHasKey('message', $retval);
        $this->assertArrayHasKey('status', $retval);
        $this->assertEquals($retval['status'], 'success');
        $this->assertArrayHasKey('current_entity', $retval);
        $this->assertInstanceOf('StdClass', $retval['current_entity']);
    }
}