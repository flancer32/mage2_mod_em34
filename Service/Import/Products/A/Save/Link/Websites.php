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
    /** @var \Em34\App\Service\Import\Products\A\Helper\Repo\Cache */
    private $hlpRepoCache;
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Em34\App\Service\Import\Products\A\Helper\Repo\Cache $hlpRepoCache
    ) {
        $this->resource = $resource;
        $this->hlpRepoCache = $hlpRepoCache;
    }

    /**
     * Save links between products & websites.
     *
     * @param array $prodIds
     */
    public function exec($prodIds)
    {
        $rows = [];
        $websiteId = $this->hlpRepoCache->getWebsiteId(Cfg::WEBSITE_CODE_BASE);
        /* compose array with data to insert */
        foreach ($prodIds as $id) {
            $row = [
                Cfg::E_CATPROD_WEBSITE_A_PRODUCT_ID => $id,
                Cfg::E_CATPROD_WEBSITE_A_WEBSITE_ID => $websiteId,
            ];
            $rows[] = $row;
        }
        $conn = $this->resource->getConnection();
        $table = $this->resource->getTableName(Cfg::ENTITY_CATALOG_PRODUCT_WEBSITE);
        $fields = [];
        $conn->insertOnDuplicate($table, $rows, $fields);
    }
}