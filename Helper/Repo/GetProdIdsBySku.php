<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App\Helper\Repo;

use Magento\Catalog\Api\Data\ProductInterface as EProd;
use Magento\Eav\Model\Entity as EntityModel;

/**
 * Get product IDs for given list with SKU (if products with SKU exist).
 */
class GetProdIdsBySku
{
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * Get product IDs for given list with SKU (if products with SKU exist).
     *
     * @param string[] $listSku
     * @return array [sku => prodId]
     */
    public function exec($listSku)
    {
        $conn = $this->resource->getConnection();
        $query = $conn->select();
        $entity = [\Magento\Catalog\Model\Product::ENTITY, 'entity'];
        $table = $this->resource->getTableName($entity);
        $query->from($table, [EntityModel::DEFAULT_ENTITY_ID_FIELD, EProd::SKU]);
        $bySku = $conn->quoteInto('sku IN (?)', $listSku);
        $query->where($bySku);
        $rs = $conn->fetchAll($query);
        $result = [];
        foreach ($rs as $one) {
            $sku = $one[EProd::SKU];
            $prodId = $one[EntityModel::DEFAULT_ENTITY_ID_FIELD];
            $result[$sku] = $prodId;
        }
        return $result;
    }
}