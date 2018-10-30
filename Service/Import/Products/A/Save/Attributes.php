<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App\Service\Import\Products\A\Save;

use Em34\App\Config as Cfg;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProdStatus;
use Magento\Catalog\Model\Product\Visibility as AVisibility;

class Attributes
{
    /** @var \Magento\Framework\Filter\TranslitUrl */
    private $filterTranslit;
    /** @var \Em34\App\Helper\Repo\GetProdIdsBySku */
    private $hlpGetProdIdsBySku;
    /** @var \Em34\App\Service\Import\Products\A\Helper\Repo\Cache */
    private $hlpRepoCache;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Filter\TranslitUrl $filterTranslit,
        \Em34\App\Helper\Repo\GetProdIdsBySku $hlpGetProdIdsBySku,
        \Em34\App\Service\Import\Products\A\Helper\Repo\Cache $hlpRepoCache
    ) {
        $this->resource = $resource;
        $this->filterTranslit = $filterTranslit;
        $this->hlpGetProdIdsBySku = $hlpGetProdIdsBySku;
        $this->hlpRepoCache = $hlpRepoCache;
    }

    /**
     * Compose data structure to be inserted into "catalog_product_entity_[datetime|decimal|text|varchar]"
     *
     * @param array $attrsToSave
     * @param array $attrsDesc
     * @param string $name
     * @param mixed $value
     * @param int $prodId
     */
    private function collectAttr(&$attrsToSave, $attrsDesc, $name, $value, $prodId)
    {
        /* don't process empty attributes */
        if (!empty(trim($value))) {
            $attrName = $attrsDesc[$name];
            $attrId = $attrName[Cfg::E_EAV_ATTRIBUTE_A_ATTRIBUTE_ID];
            $attrType = $attrName[Cfg::E_EAV_ATTRIBUTE_A_BACKEND_TYPE];
            $row = [
                Cfg::E_EAV_ALL_A_ATTRIBUTE_ID => $attrId,
                Cfg::E_EAV_ALL_A_STORE_ID => Cfg::STORE_ID_ADMIN,
                Cfg::E_EAV_ALL_A_ENTITY_ID => $prodId,
                Cfg::E_EAV_ALL_A_VALUE => $value
            ];
            $attrsToSave[$attrType][] = $row;
        }
    }

    /**
     * @param \Em34\App\Service\Import\Products\Request\Item[] $bunch
     * @param array $prodIds [sku => prodId]
     */
    public function exec($bunch, $prodIds)
    {
        /* get description of all attributes */
        $entityTypeIdProduct = $this->hlpRepoCache->getEntityTypeId(Cfg::TYPE_ENTITY_PRODUCT);
        $attrsDesc = $this->hlpRepoCache->getAttributes($entityTypeIdProduct);
        /* compose arrays with data to insert */
        $attrsToSave = [];
        $status = ProdStatus::STATUS_ENABLED;
        $taxClassId = Cfg::TAX_CLASS_ID_GOODS;
        $visibility = AVisibility::VISIBILITY_BOTH;
        foreach ($bunch as $one) {
            $product = $one->product;
            $sku = $product->sku;
            $prodId = $prodIds[$sku];
            /* regular attributes  */
            $country = $product->country;
            $description = $product->description;
            $name = $product->name;
            $price = $product->price;
            $urlKey = $this->getProductUrlKey($name);
            $weight = $product->weight;

            /* collect attributes grouped by type */
            $this->collectAttr($attrsToSave, $attrsDesc, 'country_of_manufacture', $country, $prodId);
            $this->collectAttr($attrsToSave, $attrsDesc, 'description', $description, $prodId);
            $this->collectAttr($attrsToSave, $attrsDesc, 'name', $name, $prodId);
            $this->collectAttr($attrsToSave, $attrsDesc, 'price', $price, $prodId);
            $this->collectAttr($attrsToSave, $attrsDesc, 'status', $status, $prodId);
            $this->collectAttr($attrsToSave, $attrsDesc, 'tax_class_id', $taxClassId, $prodId);
            $this->collectAttr($attrsToSave, $attrsDesc, 'url_key', $urlKey, $prodId);
            $this->collectAttr($attrsToSave, $attrsDesc, 'visibility', $visibility, $prodId);
            $this->collectAttr($attrsToSave, $attrsDesc, 'weight', $weight, $prodId);
        }

        $this->saveAttributes($attrsToSave);
    }

    private function getProductUrlKey($name)
    {
        $result = trim($name);
        $result = mb_strtolower($result);
        $result = $this->filterTranslit->filter($result);
        $result = str_replace(' ', '_', $result);
        $result = str_replace('\\', '_', $result);
        $result = str_replace('/', '_', $result);
        $result = str_replace('-', '_', $result);
        return $result;
    }

    private function saveAttributes($attrsToSave)
    {
        foreach ($attrsToSave as $type => $rows) {
            $table = Cfg::EAV_PRODUCT . $type;
            $table = $this->resource->getTableName($table);
            $conn = $this->resource->getConnection();
            $fields = [
                Cfg::E_EAV_ALL_A_VALUE
            ];
            $conn->insertOnDuplicate($table, $rows, $fields);
        }
    }
}