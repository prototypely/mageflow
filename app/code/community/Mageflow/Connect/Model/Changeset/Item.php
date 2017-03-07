<?php

/**
 * Item.php
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
 * Mageflow_Connect_Model_Changeset_Item
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 *
 * @method int getId()
 * @method string getEncoding()
 * @method string getType()
 * @method string getStatus()
 * @method string getCreatedAt()
 * @method string getUpdatedAt()
 *
 * @method setContent($value)
 * @method setEncoding($value)
 * @method setType($value)
 * @method setStatus($value) set status to one if (new, sent, rejected, failed)
 */
class Mageflow_Connect_Model_Changeset_Item extends Mage_Core_Model_Abstract implements Mageflow_Connect_Model_Interfaces_Changeitem
{

    const TYPE_CMS_BLOCK = 'cms:block';
    const TYPE_CMS_PAGE = 'cms:page';
    const TYPE_SYSTEM_CONFIGURATION = 'system:configuration';
    const TYPE_SYSTEM_ADMIN_USER = 'system:admin:user';
    const TYPE_SYSTEM_ADMIN_GROUP = 'system:admin:group';
    const TYPE_CATALOG_CATEGORY = 'catalog:category';
    const TYPE_CATALOG_PRODUCT_ATTRIBUTESET = 'catalog:product:attributeset';
    const TYPE_CATALOG_PRODUCT_ATTRIBUTE = 'catalog:product:attribute';
    const TYPE_CATALOG_ATTRIBUTESET = 'catalog:attributeset';
    const TYPE_CATALOG_ATTRIBUTE = 'catalog:attribute';
    const TYPE_CORE_WEBSITE = 'core:website';
    const TYPE_ADMIN_USER = 'admin:user';
    const TYPE_MEDIA_FILE = 'media:file';

    /**
     * @var string changeset statuses
     */
    const STATUS_NEW = 'new';
    const STATUS_SENT = 'sent';
    const STATUS_REJECTED = 'rejected';
    const STATUS_FAILED = 'failed';

    /**
     * Class constructor
     */
    public function _construct()
    {
        $this->_init('mageflow_connect/changeset_item');
    }

    /**
     * Returns MF GUID of the contents of a changeset item
     *
     * TODO
     * This method has wrong name. It should be renamed smth like
     * getContentMfGuid()
     *
     * @return string
     * @deprecated
     */
    public function getContentMfGuid()
    {
        $mfGuid = '';
        $json = $this->getContent();
        if (!empty($json)) {
            $o = json_decode($json);
            $mfGuid = $o->mf_guid;
        }
        return $mfGuid;
    }

    /**
     * Returns field value from current changesetitem's metainfo
     * @param $fieldName
     * @return null
     */
    public function getMetaInfoValue($fieldName)
    {
        $metaInfoJson = $this->getMetainfo();
        if (!empty($metaInfoJson)) {
            $o = json_decode($metaInfoJson);
            if (property_exists($o, $fieldName)) {
                return $o->$fieldName;
            }
        }
        return null;
    }

    /**
     * Returns MFGUID
     *
     * @return string
     */
    public function getMfGuid()
    {
        return $this->getData('mf_guid');
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->getData('content');
    }
}
