<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App\Service\Import\Products\A\Save;

use Em34\App\Config as Cfg;
use Magento\Catalog\Api\Data\ProductInterface as EProd;
use Magento\Eav\Model\Entity as EntityModel;

class Products
{
    /** @var \Em34\App\Service\Import\Products\A\Helper\Repo\Cache */
    private $hlpRepoCache;
    /** @var \Em34\App\Helper\Repo\GetProdIdsBySku */
    private $hlpGetProdIdsBySku;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Em34\App\Helper\Repo\GetProdIdsBySku $hlpGetProdIdsBySku,
        \Em34\App\Service\Import\Products\A\Helper\Repo\Cache $hlpRepoCache
    ) {
        $this->resource = $resource;
        $this->hlpGetProdIdsBySku = $hlpGetProdIdsBySku;
        $this->hlpRepoCache = $hlpRepoCache;
    }

    /**
     * @param \Em34\App\Service\Import\Products\Request\Item[] $bunch
     * @return array [sku => prodId]
     */
    public function exec($bunch)
    {
        $rows = [];

        $entityTypeIdProduct = $this->hlpRepoCache->getEntityTypeId(Cfg::TYPE_ENTITY_PRODUCT);
        $attrSetIdDefault = $this->hlpRepoCache->getAttributeSetId($entityTypeIdProduct);
        /* compose array with data to insert */
        foreach ($bunch as $one) {
            $sku = $one->product->sku;
            $result[] = $sku;
            $row = [
                EntityModel::DEFAULT_ENTITY_ID_FIELD => null,
                EProd::SKU => $sku,
                EProd::ATTRIBUTE_SET_ID => $attrSetIdDefault
            ];
            $rows[$sku] = $row;
        }
        /* get IDs for existing products */
        $listSku = array_keys($rows);
        $found = $this->hlpGetProdIdsBySku->exec($listSku);
        foreach ($found as $sku => $prodId) {
            $rows[$sku][EntityModel::DEFAULT_ENTITY_ID_FIELD] = $prodId;
        }
        $conn = $this->resource->getConnection();
        $entity = [\Magento\Catalog\Model\Product::ENTITY, 'entity'];
        $table = $this->resource->getTableName($entity);
        $fields = [
            EProd::ATTRIBUTE_SET_ID,
            EProd::UPDATED_AT,
        ];
        $conn->insertOnDuplicate($table, $rows, $fields);
        /* get ids for all product in the current bunch */
        $result = $this->hlpGetProdIdsBySku->exec($listSku);
        return $result;
    }
}