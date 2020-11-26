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
        foreach ($permutation as $fieldValues) {
            // Transform values in the permutation
            foreach ($fieldValues as $handle => $value) {
                $field = $configuration->getFieldByHandle($handle);

                // Assign the value
                $fields[$handle] = $value;

                // Relation fields require the value to be an array
                if( $field instanceof BaseRelationField ){
                    $fields[$handle] = [$value];
                }
            }

            // Normalize variant attributes
            $price = $configuration->normalizeSettingsValue('price', $fieldValues) ?? 0.00;
            $stock = $configuration->normalizeSettingsValue('stock', $fieldValues) ?? null;

            $variantData = [
                'price' => $price,
                'stock' => $stock,
                'minQty' => null,
                'maxQty' => null,
                'fields' => $fields
            ];

            // Populate the variant element
            $variant = ProductHelper::populateProductVariantModel($product, $variantData, 'new');

            // Save the variant
            Craft::$app->getElements()->saveElement($variant);
        }
    }
}
