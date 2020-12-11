<?php

namespace batchnz\craftcommerceuntitled\elements;

use craft\commerce\elements\Product as CommerceProduct;
use craft\commerce\records\Product as ProductRecord;

use Craft;
use craft\base\element;

class ConfigurableProduct extends CommerceProduct
{
    /**
     * Inheritdoc
     * @author Josh Smith <josh@batch.nz>
     * @return array
     */
    public function defineRules(): array
    {
        $rules = parent::defineRules();

        // Remove variant validation
        foreach ($rules as $key => $rule) {
            if( $rule[0][0] === 'variants' ){
                unset($rules[$key]);
            }
        }

        return $rules;
    }

   /**
     * @inheritdoc
     */
    public function afterSave(bool $isNew)
    {
        if (!$this->propagating) {
            if (!$isNew) {
                $record = ProductRecord::findOne($this->id);

                if (!$record) {
                    throw new Exception('Invalid product ID: ' . $this->id);
                }
            } else {
                $record = new ProductRecord();
                $record->id = $this->id;
            }

            $record->postDate = $this->postDate;
            $record->expiryDate = $this->expiryDate;
            $record->typeId = $this->typeId;
            $record->promotable = (bool)$this->promotable;
            $record->availableForPurchase = (bool)$this->availableForPurchase;
            $record->freeShipping = (bool)$this->freeShipping;
            $record->taxCategoryId = $this->taxCategoryId;
            $record->shippingCategoryId = $this->shippingCategoryId;

            $defaultVariant = $this->getDefaultVariant();
            $record->defaultVariantId = $defaultVariant->id ?? null;
            $record->defaultSku = $defaultVariant->skuAsText ?? '';
            $record->defaultPrice = $defaultVariant->price ?? 0;
            $record->defaultHeight = $defaultVariant->height ?? 0;
            $record->defaultLength = $defaultVariant->length ?? 0;
            $record->defaultWidth = $defaultVariant->width ?? 0;
            $record->defaultWeight = $defaultVariant->weight ?? 0;

            // We want to always have the same date as the element table, based on the logic for updating these in the element service i.e resaving
            $record->dateUpdated = $this->dateUpdated;
            $record->dateCreated = $this->dateCreated;

            $record->save(false);

            $this->id = $record->id;
        }

        return Element::afterSave($isNew);
    }
}
