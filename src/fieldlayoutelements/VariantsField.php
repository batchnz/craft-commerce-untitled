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

use Craft;
use craft\base\ElementInterface;
use craft\commerce\elements\Product;
use craft\commerce\helpers\VariantMatrix;
use craft\helpers\Html;
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
    /**
     * @inheritdoc
     */
    protected function inputHtml(ElementInterface $element = null, bool $static = false)
    {
        if (!$element instanceof Product) {
            throw new InvalidArgumentException('ProductTitleField can only be used in product field layouts.');
        }

        $type = $element->getType();
        if (!$type->hasVariants) {
            return null;
        }

        $columns = '';
        foreach ($type->getVariantFieldLayout()->getFields() as $field) {
            $columns .= "<th>$field->name</th>";
        }

        return <<<EOT
 <table id="configurable-variants">
  <thead>
    <tr>
      <th>SKU</th>
      <th>Quantity</th>
      <th>Price</th>
      {$columns}
    </tr>
  </thead>
</table>
EOT;
    }
}
