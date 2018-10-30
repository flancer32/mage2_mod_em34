<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App\Service\Import\Products\A\Save;

use Em34\App\Config as Cfg;
use Magento\InventoryApi\Api\Data\SourceItemInterface as AnInvStockItem;

class Inventory
{
    /** @var array see \Magento\CatalogImportExport\Model\Import\Product::$defaultStockData */
    private $defaultStockData = [
        Cfg::E_CATINV_STOCK_ITEM_A_MANAGE_STOCK => 1,
        Cfg::E_CATINV_STOCK_ITEM_A_USE_CONFIG_MANAGE_STOCK => 1,
        Cfg::E_CATINV_STOCK_ITEM_A_QTY => 0,
        Cfg::E_CATINV_STOCK_ITEM_A_MIN_QTY => 0,
        Cfg::E_CATINV_STOCK_ITEM_A_USE_CONFIG_MIN_QTY => 1,
        Cfg::E_CATINV_STOCK_ITEM_A_MIN_SALE_QTY => 1,
        Cfg::E_CATINV_STOCK_ITEM_A_USE_CONFIG_MIN_SALE_QTY => 1,
        Cfg::E_CATINV_STOCK_ITEM_A_MAX_SALE_QTY => 10000,
        Cfg::E_CATINV_STOCK_ITEM_A_USE_CONFIG_MAX_SALE_QTY => 1,
        Cfg::E_CATINV_STOCK_ITEM_A_IS_QTY_DECIMAL => 0,
        Cfg::E_CATINV_STOCK_ITEM_A_BACKORDERS => 0,
        Cfg::E_CATINV_STOCK_ITEM_A_USE_CONFIG_BACKORDERS => 1,
        Cfg::E_CATINV_STOCK_ITEM_A_NOTIFY_STOCK_QTY => 1,
        Cfg::E_CATINV_STOCK_ITEM_A_USE_CONFIG_NOTIFY_STOCK_QTY => 1,
        Cfg::E_CATINV_STOCK_ITEM_A_ENABLE_QTY_INCREMENTS => 0,
        Cfg::E_CATINV_STOCK_ITEM_A_USE_CONFIG_ENABLE_QTY_INC => 1,
        Cfg::E_CATINV_STOCK_ITEM_A_QTY_INCREMENTS => 0,
        Cfg::E_CATINV_STOCK_ITEM_A_USE_CONFIG_QTY_INCREMENTS => 1,
        Cfg::E_CATINV_STOCK_ITEM_A_IS_IN_STOCK => 1,
        Cfg::E_CATINV_STOCK_ITEM_A_LOW_STOCK_DATE => null,
        Cfg::E_CATINV_STOCK_ITEM_A_STOCK_STATUS_CHANGED_AUTO => 0,
        Cfg::E_CATINV_STOCK_ITEM_A_IS_DECIMAL_DIVIDED => 0,
    ];
    /** @var \Em34\App\Service\Import\Products\A\Helper\Repo\Cache */
    private $hlpRepoCache;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Em34\App\Service\Import\Products\A\Helper\Repo\Cache $hlpRepoCache
    ) {
        $this->resource = $resource;
        $this->hlpRepoCache = $hlpRepoCache;
    }

    /**
     * @param \Em34\App\Service\Import\Products\Request\Item[] $bunch
     * @param array $prodIds [sku => prodId]
     */
    public function exec($bunch, $prodIds)
    {
        $oldItems = []; // module-catalog-inventory
        $newItems = []; // module-inventory-api
        $websiteId = $this->hlpRepoCache->getWebsiteId(Cfg::WEBSITE_CODE_BASE);
        $stockId = $this->hlpRepoCache->getStockId(Cfg::STOCK_NAME_DEFAULT);
        foreach ($bunch as $one) {
            $product = $one->product;
            $sku = $product->sku;
            $qty = $product->qty;
            $prodId = $prodIds[$sku];
            $status = ($qty > 0) ? AnInvStockItem::STATUS_IN_STOCK : AnInvStockItem::STATUS_OUT_OF_STOCK;

            $oldRow = [
                Cfg::E_CATINV_STOCK_ITEM_A_WEBSITE_ID => $websiteId,
                Cfg::E_CATINV_STOCK_ITEM_A_STOCK_ID => $stockId,
                Cfg::E_CATINV_STOCK_ITEM_A_PRODUCT_ID => $prodId,
                Cfg::E_CATINV_STOCK_ITEM_A_QTY => $qty
            ];
            $oldRow = array_merge($this->defaultStockData, $oldRow);
            $oldItems[] = $oldRow;

            $newRow = [
                Cfg::E_INV_SOURCE_ITEM_A_SOURCE_CODE => Cfg::INV_SOURCE_CODE_DEF,
                Cfg::E_INV_SOURCE_ITEM_A_SKU => $sku,
                Cfg::E_INV_SOURCE_ITEM_A_QUANTITY => $qty,
                Cfg::E_INV_SOURCE_ITEM_A_STATUS => $status,

            ];
            $newItems[] = $newRow;
        }
        $this->saveOld($oldItems);
        $this->saveNew($newItems);
    }

    private function saveNew($toSave)
    {

        $conn = $this->resource->getConnection();
        $table = $this->resource->getTableName(Cfg::ENTITY_INVENTORY_SOURCE_ITEM);
        /* update only fields we import (skip defaults)*/
        $fields = [
            Cfg::E_INV_SOURCE_ITEM_A_QUANTITY,
            Cfg::E_INV_SOURCE_ITEM_A_STATUS
        ];
        $conn->insertOnDuplicate($table, $toSave, $fields);
    }

    private function saveOld($toSave)
    {

        $conn = $this->resource->getConnection();
        $table = $this->resource->getTableName(Cfg::ENTITY_CATALOGINVENTORY_STOCK_ITEM);
        /* update only fields we import (skip defaults)*/
        $fields = [
            Cfg::E_CATINV_STOCK_ITEM_A_QTY
        ];
        $conn->insertOnDuplicate($table, $toSave, $fields);
    }
}