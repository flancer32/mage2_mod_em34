<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App\Service\Import\Products\A\Save\Link;

use Em34\App\Config as Cfg;

class Websites
{
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * Save links between products & websites.
     *
     * @param array $prodIds
     */
    public function exec($prodIds)
    {
        $rows = [];
        /* compose array with data to insert */
        foreach ($prodIds as $id) {
//            $row = [
//                Cfg::E_CATALOG_PRODUCT_WEBSITE_A_PRODUCT_ID => $prodId,
//                Cfg::E_CATALOG_PRODUCT_WEBSITE_A_PRODUCT_ID => Cfg::WEBSITE_ADMIN,
//            ];
//            $rows[] = $row;
            $row = [
                Cfg::E_CATALOG_PRODUCT_WEBSITE_A_PRODUCT_ID => $id,
                Cfg::E_CATALOG_PRODUCT_WEBSITE_A_WEBSITE_ID => Cfg::WEBSITE_DEF,
            ];
            $rows[] = $row;
        }
        $conn = $this->resource->getConnection();
        $table = $this->resource->getTableName(Cfg::ENTITY_CATALOG_PRODUCT_WEBSITE);
        $fields = [];
        $conn->insertOnDuplicate($table, $rows, $fields);
    }
}