<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App\Service\Import;

use Em34\App\Service\Import\Products\Request as ARequest;
use Em34\App\Service\Import\Products\Response as AResponse;

class Products
{
    /** @var \Em34\App\Service\Import\Products\A\Save\Attributes */
    private $aSaveAttrs;
    /** @var \Em34\App\Service\Import\Products\A\Save\Inventory */
    private $aSaveInventory;
    /** @var \Em34\App\Service\Import\Products\A\Save\Link\Websites */
    private $aSaveLinkWebsite;
    /** @var \Em34\App\Service\Import\Products\A\Save\Products */
    private $aSaveProd;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Em34\App\Service\Import\Products\A\Save\Attributes $aSaveAttrs,
        \Em34\App\Service\Import\Products\A\Save\Inventory $aSaveInventory,
        \Em34\App\Service\Import\Products\A\Save\Link\Websites $aSaveLinkWebsite,
        \Em34\App\Service\Import\Products\A\Save\Products $aSaveProd
    ) {
        $this->resource = $resource;
        $this->aSaveAttrs = $aSaveAttrs;
        $this->aSaveInventory = $aSaveInventory;
        $this->aSaveLinkWebsite = $aSaveLinkWebsite;
        $this->aSaveProd = $aSaveProd;
    }

    public function exec($request)
    {
        /** define local working data */
        assert($request instanceof ARequest);
        $bunchSize = $request->bunchSize;
        $items = $request->items;
        $bunches = array_chunk($items, $bunchSize);
        foreach ($bunches as $bunch) {
            $this->processBunch($bunch);
        }

        /** compose result */
        $result = new AResponse();
        return $result;
    }

    private function processBunch($bunch)
    {
        $listProdIds = $this->aSaveProd->exec($bunch);
        $this->aSaveInventory->exec($bunch, $listProdIds);
        $this->aSaveLinkWebsite->exec($listProdIds);
        $this->aSaveAttrs->exec($bunch, $listProdIds);
    }

}