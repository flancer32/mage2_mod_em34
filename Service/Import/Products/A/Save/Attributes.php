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
    /** @var \Em34\App\Service\Import\Products\A\Helper\Repo\Cache */
    private $hlpRepoCache;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Filter\TranslitUrl $filterTranslit,
        \Em34\App\Service\Import\Products\A\Helper\Repo\Cache $hlpRepoCache
    ) {
        $this->resource = $resource;
        $this->filterTranslit = $filterTranslit;
        $this->hlpRepoCache = $hlpRepoCache;
    }

    /**
     * Compose data structure to be inserted into "catalog_product_entity_[datetime|decimal|text|varchar]"
     *
     * @param array $toSave
     * @param array $attrs
     * @param string $name
     * @param mixed $value
     * @param int $prodId
     */
    private function collectAttr(&$toSave, $attrs, $name, $value, $prodId)
    {
        /* don't process empty attributes */
        if (!empty(trim($value))) {
            $attrName = $attrs[$name];
            $attrId = $attrName[Cfg::E_EAV_ATTRIBUTE_A_ATTRIBUTE_ID];
            $attrType = $attrName[Cfg::E_EAV_ATTRIBUTE_A_BACKEND_TYPE];
            $row = [
                Cfg::E_EAV_ALL_A_ATTRIBUTE_ID => $attrId,
                Cfg::E_EAV_ALL_A_STORE_ID => Cfg::STORE_ID_ADMIN,
                Cfg::E_EAV_ALL_A_ENTITY_ID => $prodId,
                Cfg::E_EAV_ALL_A_VALUE => $value
            ];
            $toSave[$attrType][] = $row;
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
        $attrs = $this->hlpRepoCache->getAttributes($entityTypeIdProduct);
        /* compose arrays with data to insert */
        $toSave = [];
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
            $this->collectAttr($toSave, $attrs, 'country_of_manufacture', $country, $prodId);
            $this->collectAttr($toSave, $attrs, 'description', $description, $prodId);
            $this->collectAttr($toSave, $attrs, 'name', $name, $prodId);
            $this->collectAttr($toSave, $attrs, 'price', $price, $prodId);
            $this->collectAttr($toSave, $attrs, 'status', $status, $prodId);
            $this->collectAttr($toSave, $attrs, 'tax_class_id', $taxClassId, $prodId);
            $this->collectAttr($toSave, $attrs, 'url_key', $urlKey, $prodId);
            $this->collectAttr($toSave, $attrs, 'visibility', $visibility, $prodId);
            $this->collectAttr($toSave, $attrs, 'weight', $weight, $prodId);
        }

        $this->save($toSave);
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

    private function save($toSave)
    {
        foreach ($toSave as $type => $rows) {
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