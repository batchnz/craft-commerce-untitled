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

use batchnz\craftcommerceuntitled\Plugin;

use Craft;
use craft\behaviors\FieldLayoutBehavior;
use craft\db\ActiveRecord;

use craft\commerce\Plugin as Commerce;

/**
 * VariantConfiguration Record
 *
 * @author    Josh Smith
 * @package   CraftCommerceUntitled
 * @since     1.0.0
 */
class VariantConfigurationType extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

     /**
      * The table name for this AR class
      * @author Josh Smith <josh@batch.nz>
      * @return string
      */
    public static function tableName()
    {
        return '{{%commerce_untitled_variantconfiguration_types}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['fieldLayout'] = [
            'class' => FieldLayoutBehavior::class,
            'elementType' => VariantConfiguration::class,
        ];
        return $behaviors;
    }

    /**
     * Returns the product type for this variant configuration type
     * @author Josh Smith <josh@batch.nz>
     * @return ProductType
     */
    public function getProductType()
    {
        return Commerce::getInstance()
            ->getProductTypes()
            ->getProductTypeById($this->productTypeId);
    }

    public function rules()
    {
        return [
            [['id', 'productTypeId', 'fieldLayoutId', 'uid'], 'required']
        ];
    }
}
