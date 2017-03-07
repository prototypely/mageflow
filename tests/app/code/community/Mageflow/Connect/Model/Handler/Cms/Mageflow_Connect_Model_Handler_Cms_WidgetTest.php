<?php
/**
 * Created by PhpStorm.
 * User: urmas
 * Date: 12/4/14
 * Time: 5:15 PM
 */

class Mageflow_Connect_Model_Handler_Cms_WidgetTest  extends PHPUnit_Framework_TestCase
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
        $this->object = new Mageflow_Connect_Model_Handler_Cms_Widget();

        $this->testmodel = Mage::getModel('Mage_Widget_Model_Widget_Instance');

        $now = new Zend_Date();

        $this->testmodel->setData(array(
                "instance_type" => "reports/product_widget_viewed",
                "package_theme" => "default/iphone",
                "title" => "testing",
                "store_ids" => "1"
            ));
        $this->testmodel->setData('mf_guid', rand(111111, 999999));
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
        print_r($retval);
        $this->assertObjectHasAttribute('mf_guid', $retval);
        $this->assertObjectHasAttribute('package_theme', $retval);
        $this->assertObjectHasAttribute('instance_type', $retval);
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
        $retval = $this->object->packData(json_decode(json_encode($retval = $this->object->packData($this->testmodel)), true));

        $retval = $this->object->processData(json_decode(json_encode($retval), true));

        $this->assertArrayHasKey('message', $retval);
        $this->assertArrayHasKey('status', $retval);
        $this->assertEquals($retval['status'], 'success');
        $this->assertArrayHasKey('current_entity', $retval);
        $this->assertInstanceOf('StdClass', $retval['current_entity']);
    }
}