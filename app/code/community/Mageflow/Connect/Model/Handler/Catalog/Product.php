<?php

/**
 * Product.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Model_Handler_Catalog_Product
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Handler_Catalog_Product
    extends Mageflow_Connect_Model_Handler_Abstract
{
    /**
     * update or create catalog/product from data array
     *
     * @param $data
     *
     * @return array|null
     */
    public function processData(array $data, array $metaInfo = array())
    {
        $model = null;
        $message = null;

        try {
            $configurableAttributes = array();
            $productIsConfigurable = false;
            $productIsGrouped = false;
            $crosssellProductList = array();
            $upsellProductList = array();
            $relatedProductList = array();
            $bundleOptions = array();
            $productIsBundled = false;
            $productIsDownloadable = false;

            if (isset($data['configurable_attributes'])) {
                $configurableAttributes = $data['configurable_attributes'];
                unset($data['configurable_attributes']);
                $productIsConfigurable = true;
                $this->log('product is configurable');
            }

            if (isset($data['grouped_products'])) {
                $groupedProductList = $data['grouped_products'];
                unset($data['grouped_products']);
                $productIsGrouped = true;
            }

            if (isset($data['crosssell_products'])) {
                $crosssellProductList = $data['crosssell_products'];
                unset($data['crosssell_products']);
            }

            if (isset($data['upsell_products'])) {
                $upsellProductList = $data['upsell_products'];
                unset($data['upsell_products']);
            }

            if (isset($data['related_products'])) {
                $relatedProductList = $data['related_products'];
                unset($data['related_products']);
            }

            if (isset($data['bundle_options'])) {
                $bundleOptions = $data['bundle_options'];
                $productIsBundled = true;
                unset($data['bundle_options']);
            }

            if (isset($data['links'])) {
                $linksData = $data['links'];
                $productIsDownloadable = true;
            }

            if (isset($data['group_price'])) {
                foreach ($data['group_price'] as $key => $groupPrice) {
                    $data['group_price'][$key]['website_id'] = $this->getWebsiteIdByCode($data['group_price'][$key]['website_id']);
                    $data['group_price'][$key]['cust_group'] = $this->getCustomerGroupIdByCode($data['group_price'][$key]['cust_group']);
                }
            }

            if (isset($data['tier_price'])) {
                foreach ($data['tier_price'] as $key => $tierPrice) {
                    $data['tier_price'][$key]['website_id'] = $this->getWebsiteIdByCode($data['tier_price'][$key]['website_id']);
                    $data['tier_price'][$key]['cust_group'] = $this->getCustomerGroupIdByCode($data['tier_price'][$key]['cust_group']);
                }
            }

            if (isset($data['custom_attributes'])) {
                foreach ($data['custom_attributes'] as $attribute => $value) {
                    $data[$attribute] = $value;
                }
                unset($data['custom_attributes']);
            }

            if (isset($data['tax_class_id']) && $data['tax_class_id'] != "0") {
                $taxClass = null;
                $taxClass = Mage::getModel('tax/class')->load($data['tax_class_id'], 'mf_guid');
                if (is_null($taxClass) || $taxClass->getClassId() < 1) {
                    /**
                     * @var Mage_Tax_Model_Resource_Class_Collection $taxClassCollection
                     */
                    $taxClassCollection = Mage::getModel('tax/class')->getCollection();
                    $taxClassCollection
                        ->addFilter('class_name', $data['tax_class_id'])
                        ->addFilter('class_type', Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT);
                    $taxClass = $taxClassCollection->getFirstItem();
                }
                if (!is_null($taxClass)) {
                    $data['tax_class_id'] = $taxClass->getClassId();
                } else {
                    throw new Exception('Tax Class not found');
                }
            }

            if (isset($data['attribute_set_id'])) {
                $attributeSetEntity = null;
                $attributeSetEntity = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Set')
                    ->load($data['attribute_set_id'], 'mf_guid');

                if (!is_null($attributeSetEntity)) {
                    $data['attribute_set_id'] = $attributeSetEntity->getClassId();
                } else {
                    throw new Exception('Attribute set not found');
                }
            }

            foreach ($configurableAttributes as $configurableAttribute) {

                $productsData = array();

                $attribute = Mage::getModel('eav/entity_attribute')
                    ->getCollection()
                    ->addFieldToFilter('mf_guid', $configurableAttribute['mf_guid'])
                    ->getFirstItem();
                $attributeId = $attribute->getAttributeId();
                $usedAttributeIds[] = $attributeId;

                foreach ($configurableAttribute['values'] as $configurableAttributeValue) {
                    $attributeOption = Mage::getResourceModel(
                        'eav/entity_attribute_option_collection'
                    )
                        ->setPositionOrder('asc')
                        ->setAttributeFilter($attributeId)
                        ->addFieldToFilter('mf_guid', $configurableAttributeValue['mf_guid'])
                        ->load()
                        ->getFirstItem();

                    $configurableAttributeValue['product']['data'][$attribute->getAttributeCode()] = $attributeOption->getOptionId();

                    $relatedProduct = $this->findProduct($configurableAttributeValue['product']['identity']);

                    $productsDataEntry = array(
                        'attribute_id' => $attributeId,
                        'value_index' => $attributeOption->getOptionId(),
                        'label' => $configurableAttributeValue['label'],
                        'default_label' => $configurableAttributeValue['default_label'],
                        'store_label' => $configurableAttributeValue['store_label'],
                        'pricing_value' => $configurableAttributeValue['pricing_value'],
                        'is_percent' => $configurableAttributeValue['is_percent']
                    );
                    $productsData[$relatedProduct->getId()] = $productsDataEntry;
                }
            }

            $savedEntity = $this->processProduct($data, $metaInfo);

            if ($productIsConfigurable) {
                $this->log('setting product to configurable');
                $savedEntity->getTypeInstance()->setUsedProductAttributeIds(array($attributeId));

                $savedEntity
                    ->unsetData('_cache_instance_products')
 	                >unsetData('_cache_instance_configurable_attributes');

                $configurableAttributesData = $savedEntity->getTypeInstance()->getConfigurableAttributesAsArray();
                $savedEntity->setCanSaveConfigurableAttributes(true);
                $savedEntity->setConfigurableAttributesData($configurableAttributesData);
                $savedEntity->setConfigurableProductsData($productsData);
            }

            if ($productIsBundled) {
                $bundleOptionsData = array();
                $bundleSelectionsData = array();

                foreach ($bundleOptions as $index => $bundleOption) {
                    $bundleOptionsData[$index] = array(
                        'default_title' => $bundleOption['default_title'],
                        'title' => $bundleOption['default_title'],
                        'delete' => $bundleOption['delete'],
                        'type' => $bundleOption['type'],
                        'required' => $bundleOption['required'],
                        'option_id' => '',
                        'position' => $bundleOption['position']
                    );

                    foreach ($bundleOption['selections'] as $selectionData) {

                        $bundleProductId = $this->findProduct(array(
                            'mf_guid' => $selectionData['mf_guid'],
                            'sku' => $selectionData['sku']
                        ))->getId();

                        $bundleSelectionsData[$index][] = array(
                            'product_id' => $bundleProductId,
                            'selection_id' => '',
                            'option_id' => '',
                            'delete' => '',
                            'selection_price_value' => $selectionData['selection_price_value'],
                            'selection_price_type' => $selectionData[''],
                            'selection_qty' => $selectionData['selection_price_type'],
                            'selection_can_change_qty' => $selectionData['selection_can_change_qty'],
                            'position' => $selectionData['position'],
                            'is_default' => $selectionData['is_default']
                        );
                    }
                }

                Mage::register('product', $savedEntity);
                Mage::register('current_product', $savedEntity);

                $savedEntity->setCanSaveCustomOptions(true);
                $savedEntity->setCanSaveBundleSelections(true);
                $savedEntity->setAffectBundleProductSelections(true);
                $savedEntity->setCanSaveConfigurableAttributes(false);
                $savedEntity->setCanSaveCustomOptions(false);

                $savedEntity->setBundleOptionsData($bundleOptionsData);
                $savedEntity->setBundleSelectionsData($bundleSelectionsData);
            }

            if (isset($data['websites_data'])) {
                $savedEntity->setData('disable_creating_changeset', 1);
            }

            $savedEntity->save();

            $productId = $savedEntity->getId();
            $linkApi = Mage::getModel('catalog/product_link_api');

            if ($productIsGrouped) {
                foreach ($groupedProductList as $groupedProductIdentity) {
                    $groupedProduct = $this->findProduct($groupedProductIdentity);
                    if (!is_null($groupedProduct)) {
                        $linkApi->assign('grouped', $productId, $groupedProduct->getId());
                    }
                }
            }

            if ($productIsDownloadable) {
                foreach ($linksData as $linkData) {
                    $linkData['product_id'] = $productId;
                    $linkEntity = Mage::getModel('downloadable/link');
                    $linkEntity->setData($linkData);
                    $linkEntity->save();
                }
            }

            foreach ($crosssellProductList as $linkedProductIdentity) {
                $linkedProduct = $this->findProduct($linkedProductIdentity);
                if (!is_null($linkedProduct)) {
                    $linkApi->assign('cross_sell', $productId, $linkedProduct->getId());
                }
            }

            foreach ($upsellProductList as $linkedProductIdentity) {
                $linkedProduct = $this->findProduct($linkedProductIdentity);
                if (!is_null($linkedProduct)) {
                    $linkApi->assign('up_sell', $productId, $linkedProduct->getId());
                }
            }

            foreach ($relatedProductList as $linkedProductIdentity) {
                $linkedProduct = $this->findProduct($linkedProductIdentity);
                if (!is_null($linkedProduct)) {
                    $linkApi->assign('related', $productId, $linkedProduct->getId());
                }
            }

            if (isset($data['websites_data'])) {
                foreach ($data['websites_data'] as $storeMfGuid => $storeData) {

                    if (isset($storeData['tax_class_id'])) {
                        $storeData['tax_class_id'] = $this->mfGuidToTaxClass(
                            $storeData['tax_class_id']
                        );
                    } else {
                        if ($savedEntity->getTaxClassId()) {
                            $storeData['tax_class_id'] = $savedEntity->getTaxClassId();
                        }
                    }
                    $productModel = Mage::getModel('catalog/product')->load($savedEntity->getId());
                    $storeId = Mage::getModel('core/store')
                        ->load($storeMfGuid, 'mf_guid')
                        ->getStoreId();
                    $productModel->setStoreId($storeId);

                    foreach ($storeData as $attribute => $value) {
                        if ($value) {
                            $productModel->setExistsStoreValueFlag($attribute);
                            $productModel->setData($attribute, $value);
                        }
                    }
                    $productModel->setData('store_id', $storeId);
                    $productModel->save();
                }
            }

            return $this->sendProcessingResponse($savedEntity, $message);

        } catch (Exception $ex) {
            $this->log($ex->getMessage());
            $this->log($ex->getTraceAsString());
        }

    }

    /**
     * @param Mage_Catalog_Model_Product $model
     * @param array $optionList
     *
     * @return Mage_Catalog_Model_Product
     * @throws Exception
     */
    private function processProductOptions(Mage_Catalog_Model_Product $model, array $optionList)
    {
        /**
         * @var Mage_Catalog_Model_Product_Option $productOption
         */
        foreach ($model->getOptionInstance()->getProductOptionCollection($model) as $productOption) {
            $productOption->delete();
        }
        $model->getOptionInstance()->delete();

        foreach ($optionList as $optionSpec) {
            array_walk_recursive($optionSpec, function (&$value, $key) {
                if ($key == 'option_type_id') $value = null;
                if ($key == 'option_id') $value = null;
                if ($key == 'product_id') $value = null;
            });
            $model->getOptionInstance()->addOption($optionSpec);
        }
        return $model;
    }

    /**
     * pack content
     *
     * @param \Mage_Catalog_Model_Product|\Mage_Core_Model_Abstract $model
     *
     * @return stdClass
     */
    public function packData(Mage_Core_Model_Abstract $model)
    {
        $c = $this->mapProductAttributes($model);

        $storeDifferences = array();
        foreach ($model->getStoreIds() as $storeId) {
            /**
             * @var Mage_Catalog_Model_Product $product
             */
            $product = Mage::getModel('catalog/product')->load($model->getId());
            $product->setStoreId($storeId);
            $storeData = $this->mapProductAttributes($product);
            unset($storeData->website_ids);
            unset($storeData->product_options);
            foreach ($storeData as $attribute => $value) {
                if ($c->$attribute == $value) {
                    unset($storeData->$attribute);
                }
            }
            if (isset($storeData->tax_class_id) && $storeData->tax_class_id > 0) {
                $storeData->tax_class_id =
                    $this->taxClassToMfGuid($storeData->tax_class_id);
            }
            $storeMfGuid = Mage::getModel('core/store')
                ->load($storeId)
                ->getMfGuid();

            if (count((array)$storeData) > 0) {
                $storeDifferences[$storeMfGuid] = $storeData;
            }
        }

        $c->custom_attributes = $this->getCustomAttributes($model, $c);

        if (isset($c->type_id) && $c->type_id == 'configurable') {
            $c->configurable_attributes = $model->getTypeInstance(true)
                ->getConfigurableAttributesAsArray($model);

            foreach (
                $c->configurable_attributes as $attributeKey => $attributeData
            ) {
                $attributeId
                    = $c->configurable_attributes[$attributeKey]['attribute_id'];
                $attributeEntity = Mage::getModel('eav/entity_attribute')->load(
                    $attributeId
                );
                $attributeMfGuid = $attributeEntity->getData('mf_guid');

                $c->configurable_attributes[$attributeKey]['mf_guid']
                    = $attributeMfGuid;
                foreach (
                    $c->configurable_attributes[$attributeKey]['values'] as $key
                => $value
                ) {
                    $relatedProduct = $model->getTypeInstance()
                        ->getProductByAttributes(
                            array($attributeId => $value['value_index']), $model
                        );
                    $option = Mage::getResourceModel(
                        'eav/entity_attribute_option_collection'
                    )
                        ->addFieldToFilter('option_id', $value['value_index'])
                        ->load()
                        ->getFirstItem();

                    $c->configurable_attributes[$attributeKey]['values'][$key]['product']['identity']
                        = array
                    (
                        'sku' => $relatedProduct->getSku(),
                        'mf_guid' => $relatedProduct->getMfGuid()
                    );
                    $c->configurable_attributes[$attributeKey]['values'][$key]['mf_guid']
                        = $option->getData('mf_guid');
                }
            }
        }

        if (isset($c->type_id) && $c->type_id == 'grouped') {
            $c->grouped_products = $this->getProductListingFromIds(
                $model->getTypeInstance()->getAssociatedProductIds()
            );
        }

        if (isset($c->type_id) && $c->type_id == 'downloadable') {
            $c->links = array();
            foreach ($model->getTypeInstance()->getLinks() as $linkEntity) {
                $linkData = $linkEntity->getData();
                unset($linkData['link_id']);
                unset($linkData['product_id']);
                $c->links[] = $linkData;
            }
        }

        if (isset($c->type_id) && $c->type_id == 'bundle') {
            $c->bundle_options = array();

            $options = $model->getTypeInstance(true)->getOptionsCollection($model);

            foreach ($options as $option) {

                $selections = $model->getTypeInstance(true)->getSelectionsCollection(array($option->getOptionId()), $model);
                $selectionsData = array();

                foreach ($selections as $selection) {
                    $selectionsData[] = $selection->getData();
                }

                $optionData = $option->getData();
                $optionData['selections'] = $selectionsData;
                $c->bundle_options[] = $optionData;
            }

        }

        $c->related_products = $this->getProductListingFromIds($model->getRelatedProductIds());
        $c->crosssell_products = $this->getProductListingFromIds($model->getCrossSellProductIds());
        $c->upsell_products = $this->getProductListingFromIds($model->getUpSellProductIds());
        $c->custom_options = $model->getCustomOptions();

        foreach ($c->group_price as $key => $groupPrice) {
            $c->group_price[$key]['website_id'] = $this->getWebsiteCodeById($c->group_price[$key]['website_id']);
            $c->group_price[$key]['cust_group'] = $this->getCustomerGroupCodeById($c->group_price[$key]['cust_group']);
        }

        foreach ($c->tier_price as $key => $tierPrice) {
            $c->tier_price[$key]['website_id'] = $this->getWebsiteCodeById($c->tier_price[$key]['website_id']);
            $c->tier_price[$key]['cust_group'] = $this->getCustomerGroupCodeById($c->tier_price[$key]['cust_group']);
        }

        if (!is_null($c->tax_class_id) && $c->tax_class_id > 0) {
            $c->tax_class_id = $this->taxClassToMfGuid($c->tax_class_id);
        }

        if (!is_null($c->attribute_set_id) && $c->attribute_set_id > 0) {
            $c->attribute_set_id = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Set')
                ->load($c->attribute_set_id)
                ->getMfGuid();
        }

        $c->websites_data = $storeDifferences;
        return $c;
    }

    /**
     * map tax class id to mf_guid
     *
     * @param $taxClassId
     *
     * @return string
     */
    protected function taxClassToMfGuid($taxClassId)
    {
        $taxClass = Mage::getModel('tax/class')->load($taxClassId);
        $result = null;
        if (!is_null($taxClass)) {
            $result = $taxClass->getMfGuid();
            if (is_null($result)) {
                $result = $taxClass->getClassName();
            }
        }

        return $result;
    }

    /**
     * get tax class id from mf_guid or class name
     *
     * @param $taxClassMfGuid
     *
     * @return mixed
     */
    protected function mfGuidToTaxClass($taxClassMfGuid)
    {
        $taxClass = Mage::getModel('tax/class')->load(
            $taxClassMfGuid, 'mf_guid'
        );
        if (!is_null($taxClass)) {
            $result = $taxClass->getClassId();
        }

        if (is_null($result)) {
            $taxClass = Mage::getModel('tax/class')->load(
                $taxClassMfGuid, 'class_name'
            );
            $result = $taxClass->getClassId();
        }

        return $result;
    }

    /**
     * get listing of product identity data by id-s
     *
     * @param array $productIds
     *
     * @return array
     */
    protected function getProductListingFromIds(array $productIds)
    {
        $result = array();
        foreach ($productIds as $productId) {
            $product = Mage::getModel('catalog/product')->load($productId);
            $result[] = array(
                'sku' => $product->getSku(),
                'mf_guid' => $product->getMfGuid()
            );
        }

        return $result;
    }

    /**
     * finds Product, first try by mf_guid, then by sku
     * identityData ['sku' => sku, 'mf_guid' => mf_guid]
     *
     * @param array $identityData
     *
     * @return Mage_Catalog_Model_Product
     */
    public function findProduct(array $identityData)
    {

        /**
         * @var Mage_Catalog_Model_Product $loadedModel
         */
        $loadedModel = Mage::getModel('catalog/product');

        if (!isset($identityData['mf_guid']) && !isset($identityData['sku'])) {
            return $loadedModel;
        }

        $modelCollection = Mage::getModel('catalog/product')->getCollection();

//        if (isset($identityData['mf_guid'])) {
//            $modelCollection->addFilter('mf_guid', $identityData['mf_guid']);
//        }

//        if (!isset($identityData['mf_guid']) && isset($identityData['sku'])) {
            $modelCollection->addFilter('sku', $identityData['sku']);
//        }

        $model = $modelCollection->getFirstItem();
//        $modelId = $model->getId();
        if ($model instanceof Mage_Catalog_Model_Product && $model->getId() > 0) {
            $model->load($model->getId());
            $loadedModel = $model;
        }

        return $loadedModel;
    }

    /**
     * Maps product data to stdClass
     *
     * @param Mage_Catalog_Model_Product $model
     * @return stdClass
     */
    private function mapProductAttributes($model)
    {
        $model->getResource()->getAttribute('media_gallery')->getBackend()->afterLoad($model);
        $model->load($model->getId());

        $c = new stdClass();
        $c->group_price = array();
        $c->tier_price = array();
        $c->related_products = array();
        $c->crosssell_products = array();
        $c->upsell_products = array();
        $c->custom_options = array();
        $c->store_level_changed_fields = array();

        /**
         * @var Mageflow_Connect_Helper_Type $typeHelper
         */

        $fieldList = array_keys($model->getData());

        $changedStoreId = $model->getData('store_id');

        foreach ($fieldList as $field) {
            if ($changedStoreId) {
                if ($model->getExistsStoreValueFlag($field)) {
                    $c->$field = $model->getData($field);
                    $c->store_level_changed_fields[] = $field;
                }
            } else {
                $c->$field = $model->getData($field);
            }
        }

        $c->website_codes = array_values($this->getWebsiteCodeMap($model->getWebsiteIds()));

        $c->website_mf_guids = $this->getWebsiteMfGuidList($model->getWebsiteIds());

        $c->store_codes = $this->getStoreCodeMap($model->getStoreIds());

        $c->store_mf_guids = $this->getStoreMfGuidList($model->getStoreIds());

        if ($model->getStoreId()) {
            $c->store_id = $c->store_mf_guids[$model->getStoreId()];
        }

        if (!$changedStoreId) {
            /**
             * @var Mage_CatalogInventory_Model_Stock_Item $stockItemModel
             */
            $stockItemModel = Mage::getModel('cataloginventory/stock_item');
            $stockData = $stockItemModel->loadByProduct($model)->getData();
            ksort($stockData);
            unset($stockData['item_id']);
//        unset($stockData['stock_id']);
            unset($stockData['product_id']);
//        unset($stockData['use_config_manage_stock']);
            $stockData['use_config_manage_stock'] = 1;
//        unset($stockData['manage_stock']);
            $stockData['manage_stock'] = 1;
            $c->stock_data = $stockData;
        }

        $c->category_ids = $this->mapCategoryIdsToMfGuids($model->getCategoryIds());

        $c->store_ids = $model->getStoreIds();

        /**
         * @var Mage_Eav_Model_Entity_Attribute_Set $attributeSetModel
         */
        $attributeSetModel = Mage::getModel('eav/entity_attribute_set');
        $attributeSetModel->load($model->getAttributeSetId());
        if ($attributeSetModel->getId() < 1) {
            $attributeSetModel->load($model->getDefaultAttributeSetId());
        }
        $c->attribute_set_name = $attributeSetModel->getAttributeSetName();

        $c->product_options = $this->mapProductOptions($model);

        return $c;
    }

    /**
     * @param Mage_Catalog_Model_Product $model
     * @return array
     */
    private function mapProductOptions($model)
    {
        $optionList = array();
        if (is_array($model->getOptions())) {
            /**
             * @var Mage_Catalog_Model_Product_Option $option
             */
            foreach ($model->getOptions() as $option) {
                $data = $option->getData();
                $a = array_filter($data, function ($var) {
                    return !is_null($var);
                });
                if (is_array($option->getValues()) && sizeof($option->getValues()) > 0) {
                    /**
                     * @var Mage_Catalog_Model_Product_Option_Value $optionValue
                     */
                    $valueList = array();
                    foreach ($option->getValues() as $optionValue) {
                        $v = array_filter($optionValue->getData(), function ($var) {
                            return !is_null($var);
                        });
                        $valueList[] = $v;
                    }
                    $a['values'] = $valueList;
                }
                $optionList[] = $a;
            }
        }
        return $optionList;
    }

    /**
     * Maps category UIDs back to IDs
     * @param $uidList
     * @return array
     */
    private function mapCategoryUidsToIds($uidList)
    {
        /**
         * @var Mage_Catalog_Model_Resource_Category_Collection $collection
         */
        $collection = Mage::getModel('catalog/category')->getCollection();
        $collection->addAttributeToSelect('*')
            ->addFieldToFilter('mf_guid', array(
                'in' => $uidList
            ));
        $idList = array();
        foreach ($collection as $category) {
            $idList[] = $category->getId();
        }
        return $idList;
    }

    /**
     * This method maps integer category ids to MFGUIDs
     * @param $idList
     * @return array
     */
    private function mapCategoryIdsToMfGuids($idList)
    {
        /**
         * @var Mage_Catalog_Model_Resource_Category_Collection $collection
         */
        $collection = Mage::getModel('catalog/category')->getCollection();
        $collection->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', array(
                'in' => $idList
            ));
        $mfGuidList = array();
        foreach ($collection as $category) {
            $mfGuidList[] = $category->getMfGuid();
        }
        return $mfGuidList;
    }

    /**
     * @param Mageflow_Connect_Model_Interfaces_Changeitem $row
     * @return string|void
     */
    public function getPreview(Mageflow_Connect_Model_Interfaces_Changeitem $row)
    {
        $content = json_decode($row->getContent());
        $output = '';
        if ($content->name) {
            $output = $content->name;
        }
        return $output;
    }


    /**
     * @param $name
     * @return Mage_Eav_Model_Entity_Attribute_Set
     */
    private function findAttributeSetModel($name)
    {

        $entityType = Mage::getModel('eav/entity_type')
            ->getCollection()
            ->addFieldToFilter(
                'entity_type_code',
                array('catalog_product')
            )
            ->getFirstItem();
        /**
         * @var Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection $attributeSetModelCollection
         */
        $attributeSetModelCollection = Mage::getModel('eav/entity_attribute_set')->getCollection();
        $attributeSetModel = $attributeSetModelCollection
            ->addFieldToFilter('attribute_set_name', $name)
            ->addFieldToFilter('entity_type_id', $entityType->getId())
            ->getFirstItem();
        if (!($attributeSetModel instanceof Mage_Eav_Model_Entity_Attribute_Set) || $attributeSetModel->getId() < 1) {
            $attributeSetModel = Mage::getModel('eav/entity_attribute_set');
            $attributeSetModel->load(Mage::getModel('catalog/product')->getDefaultAttributeSetId());
        }
        return $attributeSetModel;
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws Exception
     */
    public function processProduct(array $data, array $metaInfo = array())
    {
        $data = isset($data[0]) ? $data[0] : $data;

        $identityData = array
        (
            'sku' => $data['sku'],
            'mf_guid' => $data['mf_guid']
        );

        $model = $this->findProduct($identityData);

        $incomingMediaAttributes = $data['media_attributes'];
        unset($data['media_attributes']);

        if (null === $model) {
            $model = Mage::getModel('catalog/product');
            $model->setSku('sku_' . mt_rand(100000, 999999));
        }

        unset($data['entity_id']);
        unset($data['website_ids']);
        unset($data['store_ids']);
        unset($data['stock_data']);

        $model = $this->processProductOptions($model, $data['product_options']);

        unset($data['product_options']);

        $model->setData(array_merge($model->getData(), $data));

        $attributeSetModel = $this->findAttributeSetModel(
            $data['attribute_set_name']
        );

        $model->setAttributeSetId($attributeSetModel->getId());

        $storeIds = $this->getStoreIdListByCodes($data['store_codes']);

        $model->setStoreIds($storeIds);

        $websiteIds = $this->getWebsiteIdListByCodes($data['website_codes']);

        $model->setWebsiteIds($websiteIds);

        /**
         * Map store view level values
         */
        if ($data['store_id']) {
            $storeId = Mage::getModel('core/store')->load($data['store_id'], 'mf_guid')->getId();
            $model->setStoreId($storeId);
        }
        if ($data['store_level_changed_fields']) {
            foreach ($data['store_level_changed_fields'] as $field) {
                $model->setExistsStoreValueFlag($field);
            }
        }

        foreach ($data['media_gallery']['images'] as &$image) {
            $mediaGalleryData = $model->getData('media_gallery');
            $position = 0;
            if (!is_array($mediaGalleryData)) {
                $mediaGalleryData = array(
                    'images' => array()
                );
            }

            foreach ($mediaGalleryData['images'] as &$existingImage) {
                if (isset($existingImage['position']) && $existingImage['position'] > $position) {
                    $position = $existingImage['position'];
                }
                unset($existingImage['value_id']);
            }

            $position++;
            $mediaGalleryData['images'][] = array(
                'file' => $image['file'],
                'position' => $position,
                'label' => $image['label'] ? $image['label'] : '',
                'disabled' => isset($image['exclude']) ? (int)$image['exclude'] : 0
            );

            $model->setData('media_gallery', $mediaGalleryData);

            $this->getProductMediaGallery($model)->updateImage($model, $image['file'], $data);
            $this->copyProductImage($image['file'], $metaInfo);
        }

        $model->setData('is_mageflow_import', true);

        $model->unsetData('_cache_editable_attributes');

        if (isset($data['store_ids']) && is_array($data['store_ids'])) {
            $model->setWebsiteIds($data['store_ids']);
        }

        if (isset($data['category_ids']) && is_array($data['category_ids'])) {
            $model->setCategoryIds(
                $this->mapCategoryUidsToIds($data['category_ids'])
            );
        }
        return $model;
    }

    protected function copyProductImage($file, array $metaInfo = array())
    {
        // Copy image file
        if (isset($metaInfo['secure_base_url'])) {
            $imageSourceUrl = $metaInfo['secure_base_url'] . 'media/catalog/product' . $file;
            $imageTargetPath = Mage::getBaseDir('media') . '/catalog/product' . $file;
            $sourceDate = filemtime($imageSourceUrl);
            $targetDate = filemtime($imageTargetPath);

            $client = new Zend_Http_Client($imageSourceUrl, [
                'adapter'     => 'Zend_Http_Client_Adapter_Curl',
                'curloptions' => array(CURLOPT_SSL_VERIFYPEER => false),
            ]);
            try {
                $imageData = $client->request()->getBody();
                // Override the target image when the source is newer or we don't have all last modification dates
                if ($imageData && (!$targetDate || !$sourceDate || $sourceDate > $targetDate)) {
                    mkdir(dirname($imageTargetPath), 0777, true);
                    file_put_contents($imageTargetPath, $imageData);
                }
            } catch (\Exception $ex) {
                //
            }
        }
    }

    /**
     * Returns product media gallery
     * @param $model
     * @return Mage_Catalog_Model_Product_Attribute_Backend_Media
     * @throws Error
     */
    private function getProductMediaGallery($model)
    {
        $attributeCode = 'media_gallery';
        $attributes = $model->getTypeInstance(true)->getSetAttributes($model);

        if (!isset($attributes[$attributeCode])
            || !($attributes[$attributeCode] instanceof Mage_Eav_Model_Entity_Attribute_Abstract)
        ) {
            throw new Error('Product instance does not support media');
        }
        $mediaGalleryAttribute = $attributes[$attributeCode];
        /** @var $mediaGalleryInstance Mage_Catalog_Model_Product_Attribute_Backend_Media */
        $mediaGalleryInstance = $mediaGalleryAttribute->getBackend();
        return $mediaGalleryInstance;
    }

    /**
     * Checks whether given image exists in product's media gallery
     *
     * @param $originalData
     * @param $newImagePath
     * @return bool
     */
    private function imageExists($originalData, $newImagePath)
    {
        foreach ($originalData['media_gallery']['images'] as $image) {
            if ($image['file'] == $newImagePath) {
                return true;
            }
        }
        return false;
    }

    private function getCustomAttributes($model, $c)
    {
        $attributeSetId = $model->getData('attribute_set_id');

        $attributeGroupCollection = Mage::getModel('eav/entity_attribute_group')
            ->getCollection()
            ->addFieldToFilter('attribute_set_id', $attributeSetId);

        $attributeCodeList = array();

        foreach ($attributeGroupCollection as $group) {
            $attributeList = Mage::getModel('eav/entity_attribute')
                ->getCollection()
                ->setAttributeGroupFilter(
                    $group->getAttributeGroupId()
                );

            foreach ($attributeList as $attributeEntity) {
                $attributeCodeList[$attributeEntity->getAttributeCode()]
                    = $attributeEntity->getAttributeCode();
            }
        }

        foreach ($c as $alreadySetAttribute => $alreadySetValue) {
            unset($attributeCodeList[$alreadySetAttribute]);
        }

        $customAttributes = array();
        foreach ($attributeCodeList as $attributeCode) {
            $customAttributeValue = $model->getData($attributeCode);
            if (!empty($customAttributeValue)) {
                $customAttributes[$attributeCode] = $customAttributeValue;
            }
        }

        return $customAttributes;
    }
}