<?php

namespace batchnz\craftcommerceuntitled\behaviors;

use batchnz\craftcommerceuntitled\enums\ProductVariantType;
use batchnz\craftcommerceuntitled\records\Product;

use yii\base\Behavior;

class ConfigurableProductBehavior extends Behavior
{
    /** @var Product */
    public $owner;

    /**
     * Returns the variant type for this product
     * @author Josh Smith <josh@batch.nz>
     * @return string
     */
    public function getVariantType(): string
    {
        $product = Product::findOne($this->owner->id);
        if( empty($product) ) return ProductVariantType::Standard;

        return $product->variantType;
    }
}
