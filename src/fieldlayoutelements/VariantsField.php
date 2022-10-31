<?php
/**
 * Craft Commerce Untitled plugin for Craft CMS 3.x
 *
 * Manage commerce variants by their field types and values
 *
 * @link      https://www.batch.nz
 * @copyright Copyright (c) 2020 Josh Smith
 */

namespace batchnz\craftcommerceuntitled\fieldlayoutelements;

use craft\base\ElementInterface;
use craft\commerce\elements\Product;
use yii\base\InvalidArgumentException;

use craft\commerce\fieldlayoutelements\VariantsField as CommerceVariantsField;

/**
 * VariantsField represents a Variants field that can be included within a product type’s product field layout designer.
 *
 * @author Josh Smith <josh@batch.nz>
 * @since 1.0.0
 */
class VariantsField extends CommerceVariantsField
{
    private $FIELD_TYPES = ["SKU", "Stock", "Price", "Min Qty", "Max Qty"];
    private $DIMENSIONS_DEPENDANT_FIELD_TYPES = ["Weight", "Length", "Width", "Height"];

    /**
     * @inheritdoc
     */
    protected function inputHtml(ElementInterface $element = null, bool $static = false): null|string
    {
        if (!$element instanceof Product) {
            throw new InvalidArgumentException('ProductTitleField can only be used in product field layouts.');
        }

        $type = $element->getType();
        if (!$type->hasVariants) {
            return null;
        }

        $columns = $this->getHeaderFields($element);
        foreach ($type->getVariantFieldLayout()->getCustomFields() as $field) {
            $columns .= "<th>$field->name</th>";
        }

        return <<<EOT
             <table id="configurable-variants" class="data">
              <thead>
                <tr>
                  {$columns}
                </tr>
              </thead>
            </table>
            EOT;
    }

    /**
     * @param ElementInterface $element
     * @return string Headers for the table depending on whether dimensions are enabled for the product.
     */
    protected function getHeaderFields(ElementInterface $element): string
    {
        $hasDimensions = $element->getType()->hasDimensions;
        $fields =  $hasDimensions ? array_merge($this->FIELD_TYPES, $this->DIMENSIONS_DEPENDANT_FIELD_TYPES) : $this->FIELD_TYPES;
        $columns = "";
        foreach ($fields as $field) {
            $columns .= "<th>$field</th>";
        }

        return $columns;
    }
}
