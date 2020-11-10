<?php
/**
 * Craft Commerce Untitled plugin for Craft CMS 3.x
 *
 * Manage commerce variants by their field types and values
 *
 * @link      https://www.batch.nz
 * @copyright Copyright (c) 2020 Josh Smith
 */

namespace batchnz\craftcommerceuntitled\records;

use craft\base\Element;
use craft\db\ActiveRecord;
use yii\db\ActiveQueryInterface;

/**
 * Class VariantConfiguration record.
 *
 * @property int $id ID
 * @property int $productId Product ID
 * @property int $typeId Variant Configuration Type ID
 * @property Element $element Element
 * @property Product $product Product
 * @property VariantConfigurationType $type VariantConfigurationType
 * @author Josh Smith <josh@batch.nz>
 * @since 1.0.0
 */
class VariantConfiguration extends ActiveRecord
{
    /**
     * @inheritdoc
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%commerce_untitled_variantconfigurations}}';
    }

    /**
     * Returns the entry’s element.
     *
     * @return ActiveQueryInterface The relational query object.
     */
    public function getElement(): ActiveQueryInterface
    {
        return $this->hasOne(Element::class, ['id' => 'id']);
    }

    /**
     * Returns the variant configuration's product.
     *
     * @return ActiveQueryInterface The relational query object.
     */
    public function getProduct(): ActiveQueryInterface
    {
        return $this->hasOne(Product::class, ['id' => 'productId']);
    }

    /**
     * Returns the variant configuration's type.
     *
     * @return ActiveQueryInterface The relational query object.
     */
    public function getType(): ActiveQueryInterface
    {
        return $this->hasOne(VariantConfigurationType::class, ['id' => 'typeId']);
    }
}
