<?php
/**
 * Created by PhpStorm.
 * User: urmas
 * Date: 12/4/14
 * Time: 5:15 PM
 */

class Mageflow_Connect_Model_Handler_Sales_TaxclassTest  extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mageflow_Connect_Model_Handler_Sales_Taxclass
     */
    protected $object;
    protected $testmodel;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Mageflow_Connect_Model_Handler_Sales_Taxclass();

        $this->testmodel = Mage::getModel('Mage_Tax_Model_Class');

        $now = new Zend_Date();

        $this->testmodel->setData('mf_guid', '123abc');
        $this->testmodel->setData('class_name', 'testclass');
        $this->testmodel->setData('class_type', 'CUSTOMER');
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
     * @covers Mageflow_Connect_Model_Handler_System_Design::packData
     *
     * public function packData(Mage_Core_Model_Abstract $model)
     */
    public function testPackData()
    {
        $retval = $this->object->packData($this->testmodel);

        $this->assertObjectHasAttribute('mf_guid', $retval);
        $this->assertObjectHasAttribute('class_name', $retval);
        $this->assertObjectHasAttribute('class_type', $retval);
    }

    /**
     * @covers Mageflow_Connect_Model_Handler_System_Design::getPreview
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

        $this->assertEquals($retval, 'testclass');
    }

    /**
     * @covers Mageflow_Connect_Model_Handler_System_Design::processData
     *
     * public function processData(array $data = array())
     */
    public function testProcessData()
    {
        $this->assertTrue(method_exists($this->object, 'processData'));

        $testData = json_encode($this->object->packData($this->testmodel));
        $retval = $this->object->processData(json_decode($testData, true));

        $this->assertArrayHasKey('message', $retval);
        $this->assertArrayHasKey('status', $retval);
        $this->assertEquals($retval['status'], 'success');
        $this->assertArrayHasKey('current_entity', $retval);
        $this->assertInstanceOf('StdClass', $retval['current_entity']);
    }
}