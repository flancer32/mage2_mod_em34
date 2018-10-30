<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App\Service\Import\Products\A\Helper\Repo;

use Em34\App\Config as Cfg;

/**
 * Cacheable access to repo data (ids, codes, etc...).
 */
class Cache
{
    /** @var array */
    private $cacheAttrSetIds;
    /** @var array */
    private $cacheAttributes;
    /** @var array */
    private $cacheEntityTypes;
    /** @var array */
    private $cacheWebsites;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     *
     * @param int $entityTypeId
     * @param string $name
     * @return int
     */
    public function getAttributeSetId($entityTypeId, $name = null)
    {
        if (is_null($this->cacheAttrSetIds)) {
            $conn = $this->resource->getConnection();
            $query = $conn->select();
            $table = $this->resource->getTableName(Cfg::ENTITY_EAV_ATTRIBUTE_SET);
            $cols = [
                Cfg::E_EAV_ATTRIBUTE_SET_A_ATTRIBUTE_SET_ID,
                Cfg::E_EAV_ATTRIBUTE_SET_A_ENTITY_TYPE_ID,
                Cfg::E_EAV_ATTRIBUTE_SET_A_ATTRIBUTE_SET_NAME
            ];
            $query->from($table, $cols);
            $rs = $conn->fetchAll($query);
            $this->cacheAttrSetIds = [];
            foreach ($rs as $item) {
                $itemId = $item[Cfg::E_EAV_ATTRIBUTE_SET_A_ATTRIBUTE_SET_ID];
                $itemTypeId = $item[Cfg::E_EAV_ATTRIBUTE_SET_A_ENTITY_TYPE_ID];
                $itemName = $item[Cfg::E_EAV_ATTRIBUTE_SET_A_ATTRIBUTE_SET_NAME];
                $this->cacheAttrSetIds[$itemTypeId][$itemName] = $itemId;
            }
        }
        /* return first record or by name*/
        if (is_null($name)) {
            $result = reset($this->cacheAttrSetIds[$entityTypeId]);
        } else {
            $result = $this->cacheAttrSetIds[$entityTypeId][$name];
        }
        return $result;
    }

    public function getAttributes($entityTypeId)
    {
        if (is_null($this->cacheAttributes)) {
            $conn = $this->resource->getConnection();
            $query = $conn->select();
            $table = $this->resource->getTableName(Cfg::ENTITY_EAV_ATTRIBUTE);
            $cols = [
                Cfg::E_EAV_ATTRIBUTE_A_ATTRIBUTE_ID,
                Cfg::E_EAV_ATTRIBUTE_A_ATTRIBUTE_CODE,
                Cfg::E_EAV_ATTRIBUTE_A_ENTITY_TYPE_ID,
                Cfg::E_EAV_ATTRIBUTE_A_BACKEND_TYPE
            ];
            $query->from($table, $cols);
            $rs = $conn->fetchAll($query);
            $this->cacheAttributes = [];
            foreach ($rs as $item) {
                $itemEntityTypeId = $item[Cfg::E_EAV_ATTRIBUTE_A_ENTITY_TYPE_ID];
                $itemCode = $item[Cfg::E_EAV_ATTRIBUTE_A_ATTRIBUTE_CODE];
                $this->cacheAttributes[$itemEntityTypeId][$itemCode] = $item;
            }
        }
        $result = $this->cacheAttributes[$entityTypeId];
        return $result;
    }

    /**
     * @param string $code
     * @return int
     */
    public function getEntityTypeId($code)
    {
        if (is_null($this->cacheEntityTypes)) {
            $conn = $this->resource->getConnection();
            $query = $conn->select();
            $table = $this->resource->getTableName(Cfg::ENTITY_EAV_ENTITY_TYPE);
            $cols = [
                Cfg::E_EAV_ENTITY_TYPE_A_ENTITY_TYPE_ID,
                Cfg::E_EAV_ENTITY_TYPE_A_ENTITY_TYPE_CODE
            ];
            $query->from($table, $cols);
            $rs = $conn->fetchAll($query);
            $this->cacheEntityTypes = [];
            foreach ($rs as $item) {
                $itemId = $item[Cfg::E_EAV_ENTITY_TYPE_A_ENTITY_TYPE_ID];
                $itemCode = $item[Cfg::E_EAV_ENTITY_TYPE_A_ENTITY_TYPE_CODE];
                $this->cacheEntityTypes[$itemCode] = $itemId;
            }
        }
        $result = $this->cacheEntityTypes[$code];
        return $result;
    }

    public function getWebsiteId($code)
    {
        if (is_null($this->cacheWebsites)) {
            $conn = $this->resource->getConnection();
            $query = $conn->select();
            $table = $this->resource->getTableName(Cfg::ENTITY_STORE_WEBSITE);
            $cols = [
                Cfg::E_STORE_WEBSITE_A_WEBSITE_ID,
                Cfg::E_STORE_WEBSITE_A_CODE
            ];
            $query->from($table, $cols);
            $rs = $conn->fetchAll($query);
            $this->cacheWebsites = [];
            foreach ($rs as $item) {
                $itemId = $item[Cfg::E_STORE_WEBSITE_A_WEBSITE_ID];
                $itemCode = $item[Cfg::E_STORE_WEBSITE_A_CODE];
                $this->cacheWebsites[$itemCode] = $itemId;
            }
        }
        $result = $this->cacheWebsites[$code];
        return $result;
    }
}