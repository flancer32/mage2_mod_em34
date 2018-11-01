<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App\Service\Import\Products\A\Save;

use Em34\App\Config as Cfg;

class Media
{
    /** @var \Em34\App\Service\Import\Products\A\Save\Media\A\Download */
    private $aDownload;
    /** @var int */
    private $cacheAttrIdMediaGallery;
    /** @var \Em34\App\Service\Import\Products\A\Helper\Repo\Cache */
    private $hlpRepoCache;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Em34\App\Service\Import\Products\A\Helper\Repo\Cache $hlpRepoCache,
        \Em34\App\Service\Import\Products\A\Save\Media\A\Download $aDownload
    ) {
        $this->resource = $resource;
        $this->hlpRepoCache = $hlpRepoCache;
        $this->aDownload = $aDownload;
    }

    /**
     * @param \Em34\App\Service\Import\Products\Request\Item[] $bunch
     * @param array $prodIds [sku => prodId]
     */
    public function exec($bunch, $prodIds)
    {
        $attrId = $this->getAttrIdImage();
        $rowsGallery = [];
        foreach ($bunch as $item) {
            $prod = $item->product;
            $sku = $prod->sku;
            $imageUrl = $prod->imageUrl;
            if (!empty($imageUrl)) {
                /* download file and place it into category media catalog ($filename = '/s/k/sku.jpg') */
                $fileName = $this->aDownload->exec($sku, $imageUrl);
                /* compose rows to insert into DB */
                $rowGallery = [
                    Cfg::E_CPE_MEDIA_GALLERY_A_ATTRIBUTE_ID => $attrId,
                    Cfg::E_CPE_MEDIA_GALLERY_A_VALUE => $fileName,
                    Cfg::E_CPE_MEDIA_GALLERY_A_MEDIA_TYPE => Cfg::GALLERY_MEDIA_TYPE_IMAGE,
                    Cfg::E_CPE_MEDIA_GALLERY_A_DISABLED => false,
                ];
                $rowsGallery[$sku] = $rowGallery;
            }
        }
        list($valueIdsExist, $valueIdsNew) = $this->saveGallery($rowsGallery);
        $this->saveGalleryValueEntity($rowsGallery, $prodIds, $valueIdsNew);
    }

    private function getAttrIdImage()
    {
        if (is_null($this->cacheAttrIdMediaGallery)) {
            $entityTypeIdProduct = $this->hlpRepoCache->getEntityTypeId(Cfg::TYPE_ENTITY_PRODUCT);
            $all = $this->hlpRepoCache->getAttributes($entityTypeIdProduct);
            $one = $all[Cfg::EAV_ATTR_PROD_MEDIA_GALLERY];
            $this->cacheAttrIdMediaGallery = $one[Cfg::E_EAV_ATTRIBUTE_A_ATTRIBUTE_ID];
        }
        return $this->cacheAttrIdMediaGallery;
    }

    /**
     * Get IDs of the already stored paths.
     *
     * @param string[] $values path to files stored in 'catalog_product_entity_media_gallery' as 'values'.
     * @return array [value => value_id]
     */
    private function getValueIdsForValues($values)
    {
        $result = [];
        if (count($values)) {
            $conn = $this->resource->getConnection();
            $table = $this->resource->getTableName(Cfg::ENTITY_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY);
            $query = $conn->select();
            $cols = [
                Cfg::E_CPE_MEDIA_GALLERY_A_VALUE_ID,
                Cfg::E_CPE_MEDIA_GALLERY_A_VALUE
            ];
            $query->from($table, $cols);
            $in = '';
            foreach ($values as $value) {
                $quotted = $conn->quote($value);
                $in .= "$quotted,";
            }
            $in = rtrim($in, ',');
            $byValues = Cfg::E_CPE_MEDIA_GALLERY_A_VALUE . " IN ($in)";
            $query->where($byValues);
            $rs = $conn->fetchAll($query);
            foreach ($rs as $one) {
                $id = $one[Cfg::E_CPE_MEDIA_GALLERY_A_VALUE_ID];
                $value = $one[Cfg::E_CPE_MEDIA_GALLERY_A_VALUE];
                $result[$value] = $id;
            }
        }
        return $result;
    }

    /**
     * Save data to 'catalog_product_entity_media_gallery'.
     *
     * @param $toSave
     * @return array
     */
    private function saveGallery($toSave)
    {
        /* don't save values (paths) already in gallery */
        $values = [];
        foreach ($toSave as $item) {
            $value = $item[Cfg::E_CPE_MEDIA_GALLERY_A_VALUE];
            $values[] = $value;
        }
        $idsExist = $this->getValueIdsForValues($values);

        /* exclude existing values (paths) */
        $notSaved = [];
        $keys = array_keys($idsExist);
        foreach ($toSave as $item) {
            $value = $item[Cfg::E_CPE_MEDIA_GALLERY_A_VALUE];
            if (in_array($value, $keys, true)) continue;
            $notSaved[$value] = $item;
        }
        /* save values (paths) that are not exist */
        $idsNew = [];
        if (count($notSaved)) {
            $conn = $this->resource->getConnection();
            $table = $this->resource->getTableName(Cfg::ENTITY_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY);
            /* update only fields we import (skip defaults)*/
            $fields = [];
            $conn->insertOnDuplicate($table, $notSaved, $fields);
            /* get IDs for new values (paths) */
            $valuesNew = array_keys($notSaved);
            $idsNew = $this->getValueIdsForValues($valuesNew);
        }
        return [$idsExist, $idsNew];
    }

    /**
     * Save data to 'catalog_product_entity_media_gallery_value'
     * and to 'catalog_product_entity_media_gallery_value_to_entity'.
     *
     * @param array $toSave paths to images (SKU is a key)
     * @param array $prodIds ID-by-SKU map
     * @param array $valueIdsNew valueId by value (path) map for newly inserted values
     */
    private function saveGalleryValueEntity($toSave, $prodIds, $valueIdsNew)
    {
        $rowsValue = [];
        $rowsToEntity = [];
        foreach ($toSave as $sku => $item) {
            $value = $item[Cfg::E_CPE_MEDIA_GALLERY_A_VALUE];
            /* add only new images */
            if (isset($valueIdsNew[$value])) {
                $prodId = $prodIds[$sku];
                $valueId = $valueIdsNew[$value];
                $rowValue = [
                    Cfg::E_CPEMG_VALUE_A_VALUE_ID => $valueId,
                    Cfg::E_CPEMG_VALUE_A_STORE_ID => Cfg::STORE_ID_ADMIN,
                    Cfg::E_CPEMG_VALUE_A_ENTITY_ID => $prodId,
                    Cfg::E_CPEMG_VALUE_A_POSITION => 1,
                    Cfg::E_CPEMG_VALUE_A_DISABLED => false,

                ];
                $rowsValue[] = $rowValue;
                $rowToEntity = [
                    Cfg::E_CPEMG_VALUE_TO_ENTITY_A_VALUE_ID => $valueId,
                    Cfg::E_CPEMG_VALUE_TO_ENTITY_A_ENTITY_ID => $prodId

                ];
                $rowsToEntity[] = $rowToEntity;
            }

        }
        /* save '..._media_gallery_value' */
        if (count($rowsValue)) {
            $conn = $this->resource->getConnection();
            $table = $this->resource->getTableName(Cfg::ENTITY_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE);
            /* all rows should be new */
            $fields = [];
            $conn->insertOnDuplicate($table, $rowsValue, $fields);
        }
        /* save '..._media_gallery_value_to_entity' */
        if (count($rowsToEntity)) {
            $conn = $this->resource->getConnection();
            $table = $this->resource->getTableName(Cfg::ENTITY_CATALOG_PRODUCT_ENTITY_MEDIA_GALLERY_VALUE_TO_ENTITY);
            /* all rows should be new */
            $fields = [];
            $conn->insertOnDuplicate($table, $rowsToEntity, $fields);
        }

    }

}