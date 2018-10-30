<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App\Service\Replicate\Product;

use Em34\App\Config as Cfg;
use Em34\App\Service\Replicate\Product\Save\Request as ARequest;
use Em34\App\Service\Replicate\Product\Save\Response as AResponse;

/**
 * Save one product into Magento catalog (create or update).
 */
class Save
{
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Magento\Framework\ObjectManagerInterface */
    private $manObj;
    /** @var \Magento\Catalog\Api\AttributeSetRepositoryInterface */
    private $repoAttrSet;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    private $repoProd;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Catalog\Api\AttributeSetRepositoryInterface $repoAttrSet,
        \Magento\Catalog\Api\ProductRepositoryInterface $repoProd
    ) {
        $this->logger = $logger;
        $this->manObj = $manObj;
        $this->repoAttrSet = $repoAttrSet;
        $this->repoProd = $repoProd;
    }

    private function createProduct($sku, $name, $desc, $shortDesc, $status, $qty, $price, $weight, $urlKey)
    {
        $attrSetId = $this->getAttributeSetId();
        /** @var  $product \Magento\Catalog\Api\Data\ProductInterface */
        $product = $this->manObj->create(\Magento\Catalog\Api\Data\ProductInterface::class);
        $product->setSku(trim($sku));
        $product->setName(trim($name));
        $product->setDescription($desc);
        $product->setShortDescription($shortDesc);
        $product->setStatus($status);
        $product->setPrice($price);
        $product->setQty($qty);
        $product->setIsInStock(true);
        $product->setWeight($weight);
        $product->setAttributeSetId($attrSetId);
        $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $product->setUrlKey($urlKey);
        $product->setStoreId(Cfg::STORE_ID_ADMIN);
        $websiteId = Cfg::WEBSITE_ID_BASE;
        $product->setWebsiteIds([$websiteId]);
        /* stock data */
        $isInStock = ($qty > 0);
        $stockData = [
            'is_in_stock' => $isInStock,
            'qty' => $qty
        ];
        $product->setStockData($stockData);

        /* create/update product */
        $saved = $this->repoProd->save($product);
        /* return product ID */
        $result = $saved->getId();
        return $result;
    }

    public function exec($request)
    {
        /** define local working data */
        assert($request instanceof ARequest);
        $description = $request->description;
        $descShort = $request->descShort;
        $price = $request->price;
        $qty = $request->qty;
        $name = $request->name;
        $sku = $request->sku;
        $status = $request->status;
        $urlKey = $request->urlKey;
        $weight = $request->weight;

        /** perform processing */
        $mageId = $this->createProduct(
            $sku,
            $name,
            $description,
            $descShort,
            $status,
            $qty,
            $price,
            $weight,
            $urlKey
        );

        /** compose result */
        $this->logger->info("Product '$sku' is imported (price: $price, qty: $qty).");
        $result = new AResponse();
        $result->mageId = $mageId;
        $result->name = $name;
        $result->sku = $sku;
        return $result;
    }

    /**
     * Retrieve attribute set ID.
     */
    private function getAttributeSetId()
    {
        /* TODO: attribute set ID should be cacheable */
        /** @var \Magento\Framework\Api\SearchCriteriaInterface $crit */
        $crit = $this->manObj->create(\Magento\Framework\Api\SearchCriteriaInterface::class);
        /** @var \Magento\Eav\Model\Entity\Attribute\Set $attrSet */
        $list = $this->repoAttrSet->getList($crit);
        $items = $list->getItems();
        $attrSet = reset($items);
        $result = $attrSet->getId();
        return $result;
    }
}