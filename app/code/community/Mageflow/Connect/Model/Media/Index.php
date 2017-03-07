<?php
/**
 * Index.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Model_Media_Index
 *
 * MageFlow Media Index holds list of WYSIWYG images
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 *
 * @method string getFilename()
 * @method string getBasename()
 * @method string getPath()
 * @method integer getMtime()
 * @method string getHash()
 * @method string getName()
 * @method string getShortName()
 * @method string getUrl()
 * @method integer getWidth()
 * @method integer getHeight()
 * @method string getThumbUrl()
 * @method string getType()
 * @method integer getSize()
 * @method datetime getCreatedAt()
 * @method datetime getUpdatedAt()
 * @method string getMfGuid()
 *
 * @method setFilename(string $value)
 * @method setBasename(string $value)
 * @method setPath(string $value)
 * @method setMtime(integer $value)
 * @method setHash(string $value)
 * @method setName(string $value)
 * @method setShortName(string $value)
 * @method setUrl(string $value)
 * @method setWidth(integer $value)
 * @method setHeight(integer $value)
 * @method setThumbUrl(string $value)
 * @method setType(string $value)
 * @method setSize(integer $value)
 * @method setCreatedAt(datetime $value)
 * @method setUpdatedAt(datetime $value)
 *
 */
class Mageflow_Connect_Model_Media_Index extends Mage_Core_Model_Abstract
{
    /**
     * Class constructor
     *
     * @return Mageflow_Connect_Model_Media_Index
     */
    public function _construct()
    {
        $this->_init('mageflow_connect/media_index');
        return parent::_construct();
    }


}