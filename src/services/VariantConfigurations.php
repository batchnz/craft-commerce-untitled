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

use craft\commerce\elements\Variant;
use craft\commerce\helpers\Product as ProductHelper;
use yii\db\Exception;

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

        $skus = [];
        $savedVariantIds = [];

        // Fetch existing variants that match this configuration
        $configurationVariantIds = $configuration->variants ?? [];
        $existingVariants = Variant::find()->where(['in', 'commerce_variants.id', $configurationVariantIds])->anyStatus()->all();

        // Find all SKUs of existing variants in this configuration
        $existingSkus = [];
        foreach ($existingVariants as $variant) {
            $existingSkus[$variant->getSku()] = [
                "variant" => $variant,
            ];
        }

        foreach ($permutation as $i => $fieldValues) {
            // Normalize variant attributes
            $price = $configuration->normalizeSettingsValue('price', $fieldValues, 0.00);
            $stock = $configuration->normalizeSettingsValue('stock', $fieldValues, null);
            $skus[] = $sku = $configuration->normalizeSettingsValue('sku', $fieldValues, '');
            $weight = $configuration->normalizeSettingsValue('weight', $fieldValues, null);
            $height = $configuration->normalizeSettingsValue('height', $fieldValues, null);
            $length = $configuration->normalizeSettingsValue('length', $fieldValues, null);
            $width = $configuration->normalizeSettingsValue('width', $fieldValues, null);
            $minQty = $configuration->normalizeSettingsValue('minQty', $fieldValues, null);
            $maxQty = $configuration->normalizeSettingsValue('maxQty', $fieldValues, null);

            // Normalzie variant field values
            $fields = $configuration->normalizeVariantFieldValues($fieldValues);

            // Postfix duplicate SKUs e.g. mysku-1, mysku-2 etc...
            if ($dups = array_diff_key($skus, array_unique($skus))) {
                $sku = $sku . '-' . count($dups);
            }

            $variantData = [
                'price' => $price,
                'stock' => $stock,
                'minQty' => $minQty,
                'maxQty' => $maxQty,
                'fields' => $fields,
                'sku' => $sku,
                'weight' => $weight,
                'height' => $height,
                'length' => $length,
                'width' => $width,
            ];

            // Populate the variant element
            $variant = ProductHelper::populateProductVariantModel($product, $variantData, 'new');

            // If this variant SKU already exists, update the id and the uid of the variant being created to that of the existing one.
            if (array_key_exists($sku, $existingSkus) && $existingSkus[$sku]) {
                /** @var Variant $variant */
                $existingVariant = $existingSkus[$sku]['variant'];
                $variant->id = $existingVariant->id;
                $variant->uid = $existingVariant->uid;
                $existingVariant->setEnabledForSite(true);
                // Remove this sku from the array as it has been assigned.
                unset($existingSkus[$sku]);
            }

            // Save the variant
            try {
                Craft::$app->getElements()->saveElement($variant);
            } catch (\Exception $e) {
                throw new \RuntimeException("Failed to save variant {$variant->id}: " . $e->getMessage());
            }

            $savedVariantIds[] = $variant->id;
        }

        // Delete all variants with skus that have not been assigned by the updated configuration
        foreach ($existingSkus as $existingSku) {
            /** @var Variant $variant */
            $existingVariant = $existingSku['variant'];
            $existingVariant->setEnabledForSite(false);
            Craft::$app->getElements()->saveElement($existingVariant);
            $savedVariantIds[] = $existingVariant->id;
        }

        // Update the list of variants stored against this configuration and save
        $configuration->variants = $savedVariantIds;
        Craft::$app->getElements()->saveElement($configuration);
    }

    /**
     * Delete variants for a given configuration
     * and the variant configuration itself
     * @author Daniel Siemers <daniel@batch.nz>
     * @param  VariantConfiguration $configuration
     * @return void
     */
    public function deleteVariantsByConfiguration(VariantConfiguration $configuration)
    {
        $configurationVariantIds = $configuration->variants ?? [];
        $existingVariantIds = Variant::find()->where(['in', 'commerce_variants.id', $configurationVariantIds])->ids();

        try {
            // Delete variants.
            foreach ($existingVariantIds as $elementId) {
                Craft::$app->getElements()->deleteElementById($elementId, null, null, true);
            }
        } catch (Exception $e) {
            throw new Exception('Something went wrong when deleting variants for given variant configuration:' . $e->getMessage());
        }

        try {
            // Delete variation configuration.
            Craft::$app->getElements()->deleteElementById($configuration->id, null, null, true);
        } catch (Exception $e) {
            throw new Exception('Something went wrong when deleting variant configuration:' . $e->getMessage());
        }
    }
}
