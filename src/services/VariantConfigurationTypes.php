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
use batchnz\craftcommerceuntitled\records\VariantConfigurationType;

use Craft;
use craft\base\Component;
use craft\helpers\StringHelper;
use craft\models\FieldLayout;

use craft\commerce\events\ProductTypeEvent;

use yii\base\Exception;

/**
 * VariantConfigurationTypes Service
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
class VariantConfigurationTypes extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Fetches a variant configuration type by its ID
     * @author Josh Smith <josh@batch.nz>
     * @param  int    $id
     * @return VariantConfigurationType
     */
    public function getVariantConfigurationTypeById(int $id): VariantConfigurationType
    {
        $variantConfigurationTypeRecord = VariantConfigurationType::findOne($id);

        if( empty($variantConfigurationTypeRecord) ){
            throw new Exception('Failed to load variant configuration record.');
        }

        $variantConfigurationType = new VariantConfigurationType;
        $variantConfigurationType->attributes = $variantConfigurationTypeRecord->toArray();

        return $variantConfigurationType;
    }

    /**
     * Fetches a variant configuration type by its Product ID
     * @author Josh Smith <josh@batch.nz>
     * @param  int    $productTypeId
     * @return VariantConfigurationType
     */
    public function getVariantConfigurationTypeByProductTypeId(int $productTypeId): VariantConfigurationType
    {
        $variantConfigurationType = VariantConfigurationType::findOne([
            'productTypeId' => $productTypeId
        ]);

        if( empty($variantConfigurationType) ){
            throw new Exception('Failed to load variant configuration record');
        }

        return $variantConfigurationType;
    }

    /**
     * Saves the variant configuration type
     * @author Josh Smith <josh@batch.nz>
     * @param  VariantConfigurationType $variantConfigurationType
     * @return bool
     */
    public function saveVariantConfigurationType(VariantConfigurationType $variantConfigurationType): bool
    {
        $db = Craft::$app->getDb();
        $transaction = $db->beginTransaction();

        try {
            // Fetch the variant field layout
            $variantFieldLayout = $variantConfigurationType
                ->getProductType()
                ->getVariantFieldLayout();

            // Set the field layout to the variant layout
            $variantConfigurationType->fieldLayoutId = $variantFieldLayout->id;

            // Save the record
            $result = $variantConfigurationType->save();

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        return !$result->hasErrors();
    }

    /**
     * Handles the product type after save event
     * @author Josh Smith <josh@batch.nz>
     * @param  ProductTypeEvent $e
     * @return void
     */
    public function handleProductTypeAfterSaveEvent(ProductTypeEvent $e)
    {
        $productType = $e->productType;

        try {
            $variantConfigurationType = $this->getVariantConfigurationTypeByProductTypeId($productType->id);
        } catch(Exception $e){
            $variantConfigurationType = new VariantConfigurationType([
                'productTypeId' => $productType->id
            ]);
        }

        return $this->saveVariantConfigurationType($variantConfigurationType);
    }
}
