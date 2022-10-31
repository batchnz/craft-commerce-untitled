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
use batchnz\craftcommerceuntitled\records\VariantConfigurationType;

use Craft;

use yii\rest\Controller;
use yii\web\NotFoundHttpException;

/**
 * Variant Configuration Types Controller
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Josh Smith
 * @package   CraftCommerceUntitled
 * @since     1.0.0
 */
class VariantConfigurationTypesController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/craft-commerce-untitled/variant-configuration
     *
     * @return mixed
     */
    public function actionIndex()
    {
        // Initialise a blank variant configuration query
        $variantConfigurationTypesQuery = VariantConfigurationType::find();

        if ($id = $this->request->getQueryParam('id')) {
            $variantConfigurationTypesQuery->where(['id' => $id]);
        }

        if ($productTypeId = $this->request->getQueryParam('productTypeId')) {
            $variantConfigurationTypesQuery->where(['productTypeId' => $productTypeId]);
        }

        // Fetch all matching variant configurations
        $variantConfigurationTypes = $variantConfigurationTypesQuery->all();

        return $this->asJson([
            'result' => 'success',
            'data' => $variantConfigurationTypes
        ]);
    }

    /**
     * Returns the related fields information for the requested variant configuration type
     * @author Josh Smith <josh@batch.nz>
     * @return JSON
     */
    public function actionFields()
    {
        $variantConfigurationTypesQuery = VariantConfigurationType::find();

        if ($id = $this->request->getQueryParam('id')) {
            $variantConfigurationTypesQuery->where(['id' => $id]);
        }

        if ($productTypeId = $this->request->getQueryParam('productTypeId')) {
            $variantConfigurationTypesQuery->where(['productTypeId' => $productTypeId]);
        }

        // Fetch the first matching record
        $variantConfigurationType = $variantConfigurationTypesQuery->one();

        if (empty($variantConfigurationType)) {
            throw new NotFoundHttpException('Variant Configuration Type could not be found.');
        }

        $fieldLayout = Craft::$app
            ->getFields()
            ->getLayoutById($variantConfigurationType->fieldLayoutId);

        if (empty($fieldLayout)) {
            throw new NotFoundHttpException('Variant Configuration Type Field Layout could not be found.');
        }

        // Parse out the different fields and available options from the layout
        $fieldData = Plugin::getInstance()
            ->getFields()
            ->getLayoutFieldData($fieldLayout);

        $this->asJson([
            'result' => 'success',
            'data' => $fieldData
        ]);
    }
}
