<?php
/**
 * Craft Commerce Untitled plugin for Craft CMS 3.x
 * Manage commerce variants by their field types and values
 *
 * @link      https://www.batch.nz
 * @copyright Copyright (c) 2020 Josh Smith
 */

namespace batchnz\craftcommerceuntitled\models\commerce;

use batchnz\craftcommerceuntitled\fieldlayoutelements\VariantsField;

use Craft;
use craft\helpers\ArrayHelper;
use craft\helpers\StringHelper;
use craft\models\FieldLayout;
use craft\models\FieldLayoutTab;

use craft\commerce\fieldlayoutelements\VariantsField as CommerceVariantsField;
use craft\commerce\models\ProductType as CommerceProductType;

/**
 * Product type model.
 * Extends from the Craft Commerce Product Type model
 */
class ConfigurableProductType extends CommerceProductType
{
    /**
     * Overrides the parent method to return a field layout with a configurable view.
     */
    public function getProductFieldLayout(): FieldLayout
    {
        $fieldLayout = parent::getProductFieldLayout();

        // Loop tabs that exist on this product field layout
        foreach ($fieldLayout->getTabs() as &$tab) {
            foreach ($tab->elements as $i => $element) {
                // Target elements that are instances of the commerce variants field
                if( $element instanceof CommerceVariantsField ){
                    // Swap out the the element for an instance
                    // of our own so we can render custom HTML on the layout
                    $tab->elements[$i] = new VariantsField($element);
                }
            }
        }

        return $fieldLayout;
    }
}
