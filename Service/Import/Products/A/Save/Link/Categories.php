<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App\Service\Import\Products\A\Save\Link;

use Em34\App\Config as Cfg;

/**
 * Re-link products to categories.
 */
class Categories
{
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
     * Re-link products to categories.
     *
     * @param \Em34\App\Service\Import\Products\Request\Item[] $bunch
     * @param array $prodIds [sku => prodId]
     */
    public function exec($bunch, $prodIds)
    {
        $catId = Cfg::CATALOG_CATEGORY_ROOT_DEF;
        $position = 1;
        $rows = [];
        foreach ($bunch as $item) {
            $prod = $item->product;
            $sku = $prod->sku;
            $prodId = $prodIds[$sku];
            $row = [
                Cfg::E_CATCAT_PROD_A_PRODUCT_ID => $prodId,
                Cfg::E_CATCAT_PROD_A_CATEGORY_ID => $catId,
                Cfg::E_CATCAT_PROD_A_POSITION => $position
            ];
            $rows[] = $row;
        }
        $this->save($rows);
    }

    private function save($toSave)
    {
        $conn = $this->resource->getConnection();
        $table = $this->resource->getTableName(Cfg::ENTITY_CATALOG_CATEGORY_PRODUCT);
        /* update only fields we import */
        $fields = [
            Cfg::E_CATCAT_PROD_A_CATEGORY_ID,
            Cfg::E_CATCAT_PROD_A_POSITION,
            Cfg::E_CATCAT_PROD_A_PRODUCT_ID
        ];
        $conn->insertOnDuplicate($table, $toSave, $fields);
    }
}