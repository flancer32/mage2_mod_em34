<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App\Data\Import;

/**
 * See 'catalog_product_entity' table.
 */
class Product
{
    /** @var int */
    public $attribute_set_id;
    /** @var string */
    public $created_at;
    /** @var int */
    public $entity_id;
    /** @var bool */
    public $has_options;
    /** @var float */
    public $qty;
    /** @var bool */
    public $required_options;
    /** @var string */
    public $sku;
    /** @var string */
    public $type_id;
    /** @var string */
    public $updated_at;

}