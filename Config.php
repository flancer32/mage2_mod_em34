<?php
/**
 * Container for module's constants (hardcoded configuration).
 *
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App;

use Magento\CatalogInventory\Api\Data\StockInterface as AStock;
use Magento\CatalogInventory\Api\Data\StockItemInterface as AStockItem;
use Magento\InventoryApi\Api\Data\SourceItemInterface as AInvStockItem;

class Config
{
    /** @var int TMP: ID of the root category to place all products into */
    const CATALOG_CATEGORY_ROOT_DEF = 3;

    const EAV_ATTR_PROD_MEDIA_GALLERY = 'media_gallery';

    /**
     * All tables like "%_[datetime|decimal|int|text|varchar]"
     */
    const EAV_PRODUCT = 'catalog_product_entity_';

    /**#@+
     * Magento entities/tables.
     */
    const ENTITY_CATALOGINVENTORY_STOCK = 'cataloginventory_stock';
    const ENTITY_CATALOGINVENTORY_STOCK_ITEM = 'cataloginventory_stock_item';
    const ENTITY_CATALOG_CATEGORY_PRODUCT = 'catalog_category_product';
    const ENTITY_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY = 'catalog_product_entity_media_gallery';
    const ENTITY_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE = 'catalog_product_entity_media_gallery_value';
    const ENTITY_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE_TO_ENTITY = 'catalog_product_entity_media_gallery_value_to_entity';
    const ENTITY_CATALOG_PRODUCT_WEBSITE = 'catalog_product_website';
    const ENTITY_EAV_ALL = 'all tables like "%_[datetime|decimal|int|text|varchar]"';
    const ENTITY_EAV_ATTRIBUTE = 'eav_attribute';
    const ENTITY_EAV_ATTRIBUTE_SET = 'eav_attribute_set';
    const ENTITY_EAV_ENTITY_TYPE = 'eav_entity_type';
    const ENTITY_INVENTORY_SOURCE_ITEM = \Magento\Inventory\Model\ResourceModel\SourceItem::TABLE_NAME_SOURCE_ITEM;
    const ENTITY_STORE_WEBSITE = 'store_website';
    /**$#- */

    /**#@+
     * Magento entities/tables attributes/fields.
     */
    const E_CATCAT_PROD_A_CATEGORY_ID = 'category_id';
    const E_CATCAT_PROD_A_ENTITY_ID = 'entity_id';
    const E_CATCAT_PROD_A_POSITION = 'position';
    const E_CATCAT_PROD_A_PRODUCT_ID = 'product_id';
    const E_CATINV_STOCK_A_STOCK_ID = AStock::STOCK_ID;
    const E_CATINV_STOCK_A_STOCK_NAME = AStock::STOCK_NAME;
    const E_CATINV_STOCK_A_WEBSITE_ID = 'website_id';
    const E_CATINV_STOCK_ITEM_A_BACKORDERS = AStockItem::BACKORDERS;
    const E_CATINV_STOCK_ITEM_A_ENABLE_QTY_INCREMENTS = AStockItem::ENABLE_QTY_INCREMENTS;
    const E_CATINV_STOCK_ITEM_A_IS_DECIMAL_DIVIDED = AStockItem::IS_DECIMAL_DIVIDED;
    const E_CATINV_STOCK_ITEM_A_IS_IN_STOCK = AStockItem::IS_IN_STOCK;
    const E_CATINV_STOCK_ITEM_A_IS_QTY_DECIMAL = AStockItem::IS_QTY_DECIMAL;
    const E_CATINV_STOCK_ITEM_A_ITEM_ID = AStockItem::ITEM_ID;
    const E_CATINV_STOCK_ITEM_A_LOW_STOCK_DATE = AStockItem::LOW_STOCK_DATE;
    const E_CATINV_STOCK_ITEM_A_MANAGE_STOCK = AStockItem::MANAGE_STOCK;
    const E_CATINV_STOCK_ITEM_A_MAX_SALE_QTY = AStockItem::MAX_SALE_QTY;
    const E_CATINV_STOCK_ITEM_A_MIN_QTY = AStockItem::MIN_QTY;
    const E_CATINV_STOCK_ITEM_A_MIN_SALE_QTY = AStockItem::MIN_SALE_QTY;
    const E_CATINV_STOCK_ITEM_A_NOTIFY_STOCK_QTY = AStockItem::NOTIFY_STOCK_QTY;
    const E_CATINV_STOCK_ITEM_A_PRODUCT_ID = AStockItem::PRODUCT_ID;
    const E_CATINV_STOCK_ITEM_A_QTY = AStockItem::QTY;
    const E_CATINV_STOCK_ITEM_A_QTY_INCREMENTS = AStockItem::QTY_INCREMENTS;
    const E_CATINV_STOCK_ITEM_A_STOCK_ID = AStockItem::STOCK_ID;
    const E_CATINV_STOCK_ITEM_A_STOCK_STATUS_CHANGED_AUTO = AStockItem::STOCK_STATUS_CHANGED_AUTO;
    const E_CATINV_STOCK_ITEM_A_USE_CONFIG_BACKORDERS = AStockItem::USE_CONFIG_BACKORDERS;
    const E_CATINV_STOCK_ITEM_A_USE_CONFIG_ENABLE_QTY_INC = AStockItem::USE_CONFIG_ENABLE_QTY_INC;
    const E_CATINV_STOCK_ITEM_A_USE_CONFIG_MANAGE_STOCK = AStockItem::USE_CONFIG_MANAGE_STOCK;
    const E_CATINV_STOCK_ITEM_A_USE_CONFIG_MAX_SALE_QTY = AStockItem::USE_CONFIG_MAX_SALE_QTY;
    const E_CATINV_STOCK_ITEM_A_USE_CONFIG_MIN_QTY = AStockItem::USE_CONFIG_MIN_QTY;
    const E_CATINV_STOCK_ITEM_A_USE_CONFIG_MIN_SALE_QTY = AStockItem::USE_CONFIG_MIN_SALE_QTY;
    const E_CATINV_STOCK_ITEM_A_USE_CONFIG_NOTIFY_STOCK_QTY = AStockItem::USE_CONFIG_NOTIFY_STOCK_QTY;
    const E_CATINV_STOCK_ITEM_A_USE_CONFIG_QTY_INCREMENTS = AStockItem::USE_CONFIG_QTY_INCREMENTS;
    const E_CATINV_STOCK_ITEM_A_WEBSITE_ID = 'website_id';
    const E_CATPROD_WEBSITE_A_PRODUCT_ID = 'product_id';
    const E_CATPROD_WEBSITE_A_WEBSITE_ID = 'website_id';
    const E_CPEMG_VALUE_A_DISABLED = 'disabled';
    const E_CPEMG_VALUE_A_ENTITY_ID = 'entity_id';
    const E_CPEMG_VALUE_A_LABEL = 'label';
    const E_CPEMG_VALUE_A_POSITION = 'position';
    const E_CPEMG_VALUE_A_RECORD_ID = 'record_id';
    const E_CPEMG_VALUE_A_STORE_ID = 'store_id';
    const E_CPEMG_VALUE_A_VALUE_ID = 'value_id';
    const E_CPEMG_VALUE_TO_ENTITY_A_ENTITY_ID = 'entity_id';
    const E_CPEMG_VALUE_TO_ENTITY_A_VALUE_ID = 'value_id';
    const E_CPE_MEDIA_GALLERY_A_ATTRIBUTE_ID = 'attribute_id';
    const E_CPE_MEDIA_GALLERY_A_DISABLED = 'disabled';
    const E_CPE_MEDIA_GALLERY_A_MEDIA_TYPE = 'media_type';
    const E_CPE_MEDIA_GALLERY_A_VALUE = 'value';
    const E_CPE_MEDIA_GALLERY_A_VALUE_ID = 'value_id';
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
    const E_INV_SOURCE_ITEM_A_QUANTITY = AInvStockItem::QUANTITY;
    const E_INV_SOURCE_ITEM_A_SKU = AInvStockItem::SKU;
    const E_INV_SOURCE_ITEM_A_SOURCE_CODE = AInvStockItem::SOURCE_CODE;
    const E_INV_SOURCE_ITEM_A_STATUS = AInvStockItem::STATUS;
    const E_STORE_WEBSITE_A_CODE = 'code';
    const E_STORE_WEBSITE_A_WEBSITE_ID = 'website_id';
    /**$#- */

    const GALLERY_MEDIA_TYPE_IMAGE = 'image';

    const INV_SOURCE_CODE_DEF = 'default';

    /** This module name. */
    const MODULE = self::MODULE_VENDOR . '_' . self::MODULE_PACKAGE;
    const MODULE_PACKAGE = 'App';
    const MODULE_VENDOR = 'Em34';
    const STOCK_ID_DEF = 1;
    const STOCK_NAME_DEFAULT = 'Default';
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