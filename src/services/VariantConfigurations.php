<?php
/**
 * Craft Commerce Untitled plugin for Craft CMS 3.x
 *
 * Manage commerce variants by their field types and values
 *
 * @link      https://www.batch.nz
 * @copyright Copyright (c) 2020 Josh Smith
 */

namespace batchnz\craftcommerceuntitled\services;

use batchnz\craftcommerceuntitled\Plugin;
use batchnz\craftcommerceuntitled\elements\VariantConfiguration;

use Craft;
use craft\base\Component;
use craft\fields\BaseRelationField;

use craft\commerce\elements\Variant;
use craft\commerce\helpers\Product as ProductHelper;

/**
 * VariantConfigurations Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Josh Smith
 * @package   CraftCommerceUntitled
 * @since     1.0.0
 */
class VariantConfigurations extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Get a configuration by ID.
     *
     * @param int $id
     * @param int $siteId
     * @return VariantConfiguration|null
     */
    public function getVariantConfigurationById(int $id, $siteId = null): ?VariantConfiguration
    {
        /** @var VariantConfiguration $variantConfiguration */
        return Craft::$app
            ->getElements()
            ->getElementById($id, VariantConfiguration::class, $siteId);
    }

    /**
     * Generates variants by the configuration
     * @author Josh Smith <josh@batch.nz>
     * @param  VariantConfiguration $configuration
     * @return void
     */
    public function generateVariantsByConfiguration(VariantConfiguration $configuration)
    {
        $product = $configuration->getProduct();
        $permutation = $configuration->getVariantPermutations();

        $fields = [];
        foreach ($permutation as $i => $fieldValues) {
            // Transform values in the permutation
            foreach ($fieldValues as $handle => $value) {
                $field = $configuration->getFieldByHandle($handle);

                // Relation fields require the value to be an array
                if( $field instanceof BaseRelationField ){
                    $value = [$value];
                }

                // Assign the value
                $fields[$i][$handle] = $value;
            }
        }

        // Delete existing variants
        foreach ($fields as $field) {
            foreach ($field as $fieldHandle => $values) {
                $matchingVariants = Variant::find()->{$fieldHandle}($values)->all();
                foreach ($matchingVariants as $variant) {
                    Craft::$app->getElements()->deleteElement($variant, true);
                }
            }
        }

        // Create new Variants
        $skus = [];
        foreach ($permutation as $i => $fieldValues) {
            // Normalize variant attributes
            $stock = $configuration->normalizeSettingsValue('stock', $fieldValues) ?? null;
            $price = $configuration->normalizeSettingsValue('price', $fieldValues) ?? 0.00;
            $skus[] = $sku = $configuration->normalizeSettingsValue('sku', $fieldValues) ?? '';

            // Postfix duplicate SKUs e.g. mysku-1, mysku-2 etc...
            if( $dups = array_diff_key($skus, array_unique($skus)) ){
                $sku = $sku . '-' . count($dups);
            }

            $variantData = [
                'price' => $price,
                'stock' => $stock,
                'minQty' => null,
                'maxQty' => null,
                'fields' => $fields[$i],
                'sku' => $sku
            ];

            // Populate the variant element
            $variant = ProductHelper::populateProductVariantModel($product, $variantData, 'new');

            // Save the variant
            Craft::$app->getElements()->saveElement($variant);
        }
    }
}
