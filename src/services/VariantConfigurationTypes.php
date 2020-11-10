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
use batchnz\craftcommerceuntitled\models\VariantConfigurationType;
use batchnz\craftcommerceuntitled\records\VariantConfigurationType as VariantConfigurationTypeRecord;

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
        $variantConfigurationTypeRecord = VariantConfigurationTypeRecord::findOne($id);

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
        $variantConfigurationTypeRecord = VariantConfigurationTypeRecord::findOne(['productTypeId' => $productTypeId]);

        if( empty($variantConfigurationTypeRecord) ){
            throw new Exception('Failed to load variant configuration record');
        }

        $variantConfigurationType = new VariantConfigurationType;
        $variantConfigurationType->attributes = $variantConfigurationTypeRecord->toArray();

        return $variantConfigurationType;
    }

    /**
     * Saves the variant configuration type
     * TODO: add validation
     * @author Josh Smith <josh@batch.nz>
     * @param  VariantConfigurationType $variantConfigurationType
     * @return bool
     */
    public function saveVariantConfigurationType(VariantConfigurationType $variantConfigurationType): bool
    {
        $db = Craft::$app->getDb();
        $transaction = $db->beginTransaction();

        // Attempt to load an existing record
        $variantConfigurationTypeRecord = VariantConfigurationTypeRecord::findOne($variantConfigurationType->id);

        // Create a new record
        if( empty($variantConfigurationTypeRecord) ){
            $variantConfigurationTypeRecord = new VariantConfigurationTypeRecord();
        }

        try {
            // Fetch the variant field layout
            $variantFieldLayout = $variantConfigurationType->getProductType()->getVariantFieldLayout();

            // Create a new field layout from the variant layout config
            $fieldLayout = FieldLayout::createFromConfig($variantFieldLayout->getConfig());
            $fieldLayout->type = VariantConfiguration::class;

            // Assign a UID to the field layout if we're creating a new one
            if( empty($variantConfigurationType->fieldLayoutId) ){
                $fieldLayout->uid = StringHelper::UUID();
            // Otherwise, simply replace the ID attribute to update an existing record
            } else {
                $fieldLayout->id = $variantConfigurationType->fieldLayoutId;
            }

            // Save the layout
            Craft::$app->getFields()->saveLayout($fieldLayout);

            // Assign properties to the record for saving
            $variantConfigurationType->fieldLayoutId = $fieldLayout->id;
            $variantConfigurationTypeRecord->productTypeId = $variantConfigurationType->productTypeId;
            $variantConfigurationTypeRecord->fieldLayoutId = $variantConfigurationType->fieldLayoutId;

            // Save the record
            $variantConfigurationTypeRecord->save();

            // Set additional properties
            $variantConfigurationType->id = $variantConfigurationTypeRecord->id;
            $variantConfigurationType->uid = $variantConfigurationTypeRecord->uid;

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        return true;
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
