<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App\Service\Import\Products\Request\Item;

class Product
    extends \Em34\App\Data\Import\Product
{
    /** @var string */
    public $country;
    /** @var string */
    public $description;
    /** @var string */
    public $name;
    /** @var float */
    public $price;
    /** @var float */
    public $weight;
}