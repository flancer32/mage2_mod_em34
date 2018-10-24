<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App\Service\Import\Products;


class Request
{
    /** @var int size of the max count of the items to be processed at once in the loop on import */
    public $bunchSize;
    /** @var \Em34\App\Service\Import\Products\Request\Item[] product items to import */
    public $items;

}