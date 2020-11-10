<?php

namespace batchnz\craftcommerceuntitled\models;

use batchnz\craftcommerceuntitled\elements\VariantConfiguration;

use Craft;
use craft\base\Model;
use craft\behaviors\FieldLayoutBehavior;

use craft\commerce\Plugin as Commerce;

/**
 * Model that represents a variant configuration type
 */
class VariantConfigurationType extends Model
{
    /**
     * @var int|null ID
     */
    public $id;

    /**
     * @var int|null Product Type ID
     */
    public $productTypeId;

    /**
     * @var int|null Field layout ID
     */
    public $fieldLayoutId;

    /**
     * @var string UID
     */
    public $uid;

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
        return Commerce::getInstance()->getProductTypes()->getProductTypeById($this->productTypeId);
    }

    public function rules()
    {
        return [
            [['id', 'productTypeId', 'fieldLayoutId', 'uid'], 'required']
        ];
    }
}