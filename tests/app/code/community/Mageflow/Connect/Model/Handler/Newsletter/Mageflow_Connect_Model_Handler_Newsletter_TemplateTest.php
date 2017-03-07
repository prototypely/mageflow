<?php
/**
 * Created by PhpStorm.
 * User: urmas
 * Date: 12/4/14
 * Time: 5:15 PM
 */

class Mageflow_Connect_Model_Handler_Newsletter_TemplateTest  extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mageflow_Connect_Model_Handler_Newsletter_Template
     */
    protected $object;
    protected $testmodel;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
//        $this->object =  Mage::getModel('Mage_Core_Model_Design');
        $this->object = new Mageflow_Connect_Model_Handler_Newsletter_Template();

        $this->testmodel = Mage::getModel('Mage_Newsletter_Model_Template');

        $now = new Zend_Date();

        $this->testmodel->setData(
            array(
                'created_at'            => '2014-12-11T10=>42=>42+00=>00',
                'mf_guid'               => 'syxkd17lqruzfqgjq7m8m8bz9zdp1bxx',
                'template_actual'       => 1,
                'template_code'         => '2',
                'template_sender_email' => 'support@example.com',
                'template_sender_name'  => 'CustomerSupport',
                'template_styles'       => '',
                'template_subject'      => '2',
                'template_text'         => 'testing',
                'template_type'         => 2,
                'updated_at'            => '2014-12-11T10=>42=>42+00=>00'
            )
        );
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
        $this->assertObjectHasAttribute('template_actual', $retval);
        $this->assertObjectHasAttribute('template_code', $retval);
        $this->assertObjectHasAttribute('template_sender_email', $retval);
        $this->assertObjectHasAttribute('template_sender_name', $retval);
        $this->assertObjectHasAttribute('template_styles', $retval);
        $this->assertObjectHasAttribute('template_text', $retval);
        $this->assertObjectHasAttribute('template_type', $retval);
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

        $this->assertEquals($retval, '2');
    }

    /**
     * @covers Mageflow_Connect_Model_Handler_System_Design::processData
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