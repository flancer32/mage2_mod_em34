<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App\Service\Import\Products\A\Helper;

/**
 * Retrieve product import configuration parameters from app.
 */
class Configurator
{
    /** @var \Magento\Framework\Api\SearchCriteriaBuilder */
    private $builderSearchCrit;
    /** @var int */
    private $cacheAttrSetId;
    /** @var \Magento\Catalog\Api\AttributeSetRepositoryInterface */
    private $repoAttrSet;

    public function __construct(
        \Magento\Framework\Api\SearchCriteriaBuilder $builderSearchCrit,
        \Magento\Catalog\Api\AttributeSetRepositoryInterface $repoAttrSet
    ) {
        $this->builderSearchCrit = $builderSearchCrit;
        $this->repoAttrSet = $repoAttrSet;
    }

    public function getAttributeSetId()
    {
        if (is_null($this->cacheAttrSetId)) {
            $search = $this->builderSearchCrit->create();
            /** @var \Magento\Eav\Model\Entity\Attribute\Set $attrSet */
            $list = $this->repoAttrSet->getList($search);
            $items = $list->getItems();
            $attrSet = reset($items);
            $this->cacheAttrSetId = $attrSet->getId();
        }
        return $this->cacheAttrSetId;
    }

    public function getWebsiteId()
    {
        if (is_null($this->cacheAttrSetId)) {
            $search = $this->builderSearchCrit->create();
            /** @var \Magento\Eav\Model\Entity\Attribute\Set $attrSet */
            $list = $this->repoAttrSet->getList($search);
            $items = $list->getItems();
            $attrSet = reset($items);
            $this->cacheAttrSetId = $attrSet->getId();
        }
        return $this->cacheAttrSetId;
    }
}