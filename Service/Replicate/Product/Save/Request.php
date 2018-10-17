<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App\Service\Replicate\Product\Save;


class Request
{
    /** @var \Em34\App\Service\Replicate\Product\Save\Request\Attribute[] */
    public $attributes;
    /** @var string */
    public $descShort;
    /** @var string */
    public $description;
    /** @var string */
    public $name;
    /** @var float */
    public $price;
    /** @var int */
    public $qty;
    /** @var string */
    public $sku;
    /** @var string */
    public $status;
    /** @var string */
    public $urlKey;
    /** @var float */
    public $weight;
}