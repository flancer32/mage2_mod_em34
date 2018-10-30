<?php
/**
 * Container for module's constants (hardcoded configuration).
 *
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App;

class Config
{
    /**
     * All tables like "%_[datetime|decimal|int|text|varchar]"
     */
    const EAV_PRODUCT = 'catalog_product_entity_';

    /**#@+
     * Magento entities/tables.
     */
    const ENTITY_CATALOG_PRODUCT_WEBSITE = 'catalog_product_website';
    const ENTITY_EAV_ALL = 'all tables like "%_[datetime|decimal|int|text|varchar]"';
    const ENTITY_EAV_ATTRIBUTE = 'eav_attribute';
    const ENTITY_EAV_ATTRIBUTE_SET = 'eav_attribute_set';
    const ENTITY_EAV_ENTITY_TYPE = 'eav_entity_type';
    const ENTITY_STORE_WEBSITE = 'store_website';
    /**$#- */

    /**#@+
     * Magento entities/tables attributes/fields.
     */
    const E_CATALOG_PRODUCT_WEBSITE_A_PRODUCT_ID = 'product_id';
    const E_CATALOG_PRODUCT_WEBSITE_A_WEBSITE_ID = 'website_id';
    const E_EAV_ALL_A_ATTRIBUTE_ID = 'attribute_id';
    const E_EAV_ALL_A_ENTITY_ID = 'entity_id';
    const E_EAV_ALL_A_STORE_ID = 'store_id';
    const E_EAV_ALL_A_VALUE = 'value';
    const E_EAV_ALL_A_VALUE_ID = 'value_id';
    const E_EAV_ATTRIBUTE_A_ATTRIBUTE_CODE = 'attribute_code';
    const E_EAV_ATTRIBUTE_A_ATTRIBUTE_ID = 'attribute_id';
    const E_EAV_ATTRIBUTE_A_BACKEND_TYPE = 'backend_type';
    const E_EAV_ATTRIBUTE_A_ENTITY_TYPE_ID = 'entity_type_id';
    const E_EAV_ATTRIBUTE_SET_A_ATTRIBUTE_SET_ID = 'attribute_set_id';
    const E_EAV_ATTRIBUTE_SET_A_ATTRIBUTE_SET_NAME = 'attribute_set_name';
    const E_EAV_ATTRIBUTE_SET_A_ENTITY_TYPE_ID = 'entity_type_id';
    const E_EAV_ENTITY_TYPE_A_ENTITY_TYPE_CODE = 'entity_type_code';
    const E_EAV_ENTITY_TYPE_A_ENTITY_TYPE_ID = 'entity_type_id';
    const E_STORE_WEBSITE_A_CODE = 'code';
    const E_STORE_WEBSITE_A_WEBSITE_ID = 'website_id';
    /**$#- */

    /** This module name. */
    const MODULE = self::MODULE_VENDOR . '_' . self::MODULE_PACKAGE;
    const MODULE_PACKAGE = 'App';
    const MODULE_VENDOR = 'Em34';

    const STOCK_ID_DEF = 1;
    const STORE_CODE_ADMIN = 'admin';
    const STORE_CODE_DEF = 'default';
    const STORE_ID_ADMIN = 0;
    const STORE_ID_DEF = 1;

    /**$#+
     * Tax class IDs (see table "tax_class")
     */
    const TAX_CLASS_ID_CUSTOMER= 3;
    const TAX_CLASS_ID_GOODS = 2;
    /**$#- */


    /**#@+
     * Codes for entities types in 'eav_entity_type'.
     */
    const TYPE_ENTITY_CATEGORY = \Magento\Catalog\Model\Category::ENTITY;
    const TYPE_ENTITY_CREDITMEMO = 'creditmemo';
    const TYPE_ENTITY_CUSTOMER = \Magento\Customer\Model\Customer::ENTITY;
    const TYPE_ENTITY_CUST_ADDR = 'customer_address';
    const TYPE_ENTITY_INVOICE = 'invoice';
    const TYPE_ENTITY_PRODUCT = \Magento\Catalog\Model\Product::ENTITY;
    const TYPE_ENTITY_SALE = \Magento\Sales\Model\Order::ENTITY;
    const TYPE_ENTITY_SHIPMENT = 'shipment';
    const WEBSITE_CODE_ADMIN = 'admin';
    const WEBSITE_CODE_BASE = 'base';
    /** @deprecated  use \Em34\App\Service\Import\Products\A\Helper\Repo\Cache::getWebsiteId */
    const WEBSITE_ID_BASE = 1;
}