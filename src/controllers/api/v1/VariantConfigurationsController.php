<?php
/**
 * Craft Commerce Untitled plugin for Craft CMS 3.x
 *
 * Manage commerce variants by their field types and values
 *
 * @link      https://www.batch.nz
 * @copyright Copyright (c) 2020 Josh Smith
 */

namespace batchnz\craftcommerceuntitled\controllers\api\v1;

use batchnz\craftcommerceuntitled\Plugin;
use batchnz\craftcommerceuntitled\elements\VariantConfiguration as VariantConfigurationModel;
use batchnz\craftcommerceuntitled\helpers\VariantConfiguration as VariantConfigurationHelper;

use Craft;

use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

/**
 * Variant Configurations Controller
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Josh Smith
 * @package   CraftCommerceUntitled
 * @since     1.0.0
 */
class VariantConfigurationsController extends Controller
{

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL
     * @author Josh Smith <josh@batch.nz>
     * @return JSON
     */
    public function actionIndex()
    {
        // Initialise a blank variant configuration query
        $variantConfigurationsQuery = VariantConfigurationModel::find();

        // Filter on product Ids
        if( $productId = $this->request->getQueryParam('productId') ){
            $variantConfigurationsQuery->productId($productId);
        }

        // Fetch all matching variant configurations
        $variantConfigurations = $variantConfigurationsQuery->all();

        return $this->asJson([
            'result' => 'success',
            'data' => $variantConfigurations
        ]);
    }

    /**
     * Endpoint to generate variants for the configuration
     * @author Josh Smith <josh@batch.nz>
     * @param  int    $configurationId ID of the configuration to generate variants for
     * @return JSON
     */
    public function actionGenerateVariants(int $id)
    {
        $configuration = VariantConfigurationModel::find()
            ->id($id)
            ->one();

        if( empty($configuration) )
            throw new NotFoundHttpException('Variant Configuration not found.');

        // Create variants from the configuration selections
        Plugin::getInstance()
            ->getVariantConfigurations()
            ->generateVariantsByConfiguration($configuration);

        return $this->asJson([
            'result' => 'success',
        ]);
    }

    /**
     * Handles the saving of product variant configurations
     * @author Josh Smith <josh@batch.nz>
     * @return JSON
     */
    public function actionSave()
    {
        // Enforce POST
        if (!$this->request->getIsPost()) {
            throw new BadRequestHttpException('Post request required');
        }

        $variantConfiguration = VariantConfigurationHelper::populateVariantConfigurationFromPost();

        return $this->save($variantConfiguration);
    }

     /**
     * PATCH endpoint to save variant configuration fields
     * @author Josh Smith <josh@batch.nz>
     * @return JSON
     */
    public function actionSaveFields($id = null)
    {
        // Enforce PATCH
        if (!$this->request->getIsPatch()) {
            throw new BadRequestHttpException('Patch request required');
        }

        // Enforce that fields have been passed
        $fields = $this->request->getBodyParam('fields');
        if( empty($fields) ){
            throw new BadRequestHttpException('No fields received.');
        }

        // Remove superfluous data
        $this->request->setBodyParams([
            'variantConfigurationId' => $id,
            'fields' => $fields
        ]);

        // Save the field selection scenario
        $variantConfiguration = VariantConfigurationHelper::populateVariantConfigurationFromPost();
        $variantConfiguration->setScenario(VariantConfigurationModel::SCENARIO_SET_FIELDS);

        return $this->save($variantConfiguration);
    }

    /**
     * Saves variant field values
     * @author Josh Smith <josh@batch.nz>
     * @param  int $id
     * @return JSON
     */
    public function actionSaveValues($id = null)
    {
        // Enforce PATCH
        if (!$this->request->getIsPatch()) {
            throw new BadRequestHttpException('Patch request required');
        }

        // Enforce that values have been passed
        $values = $this->request->getBodyParam('values');
        if( empty($values) ){
            throw new BadRequestHttpException('No values received.');
        }

        // Remove superfluous data
        $this->request->setBodyParams([
            'variantConfigurationId' => $id,
            'values' => $values
        ]);

        // Save the field selection scenario
        $variantConfiguration = VariantConfigurationHelper::populateVariantConfigurationFromPost();
        $variantConfiguration->setScenario(VariantConfigurationModel::SCENARIO_SET_VALUES);

        return $this->save($variantConfiguration);
    }

    /**
     * PATCH endpoint to save variant configuration settings
     * @author Josh Smith <josh@batch.nz>
     * @return JSON
     */
    public function actionSaveSettings($id = null)
    {
        // Enforce PATCH
        if (!$this->request->getIsPatch()) {
            throw new BadRequestHttpException('Patch request required');
        }

        // Enforce that settings have been passed
        $settings = $this->request->getBodyParam('settings');
        if( empty($settings) ){
            throw new BadRequestHttpException('No settings received.');
        }

        // Remove superfluous data
        $this->request->setBodyParams([
            'variantConfigurationId' => $id,
            'settings' => $settings
        ]);

        // Save the field selection scenario
        $variantConfiguration = VariantConfigurationHelper::populateVariantConfigurationFromPost();
        $variantConfiguration->setScenario(VariantConfigurationModel::SCENARIO_SET_SETTINGS);

        return $this->save($variantConfiguration);
    }

    // Protected Methods
    // =========================================================================

    /**
     * Internal method to save the element whilst handling validatione errors etc.
     * @author Josh Smith <josh@batch.nz>
     * @param  VariantConfigurationModel $variantConfiguration
     * @return JSON
     */
    protected function save(VariantConfigurationModel $variantConfiguration)
    {
        // Save it.
        $result = Craft::$app->getElements()->saveElement($variantConfiguration);

        if( $result === false ){
            return $this->asJson([
                'result' => 'validation_error',
                'errors' => $variantConfiguration->getErrors()
            ])->setStatusCode(422);
        }

        return $this->asJson([
            'result' => 'success',
            'data' => $variantConfiguration
        ]);
    }
}
