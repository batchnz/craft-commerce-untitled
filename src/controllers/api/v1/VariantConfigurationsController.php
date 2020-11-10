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

use Craft;

use yii\rest\Controller;
use yii\web\BadRequestHttpException;

/**
 * VariantConfigurations Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
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
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/craft-commerce-untitled/variant-configuration
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $result = 'Welcome to the VariantConfigurationsController actionIndex() method';
        return $this->asJson($result);
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

        $elementsService = Craft::$app->getElements();
        $siteId = Craft::$app->getSites()->getCurrentSite()->id;

        // Extract common POST data
        $id = $this->request->getBodyParam('variantConfigurationId');
        $typeId = $this->request->getBodyParam('typeId');
        $productId = $this->request->getBodyParam('productId');

        // Enforce permissions... TODO

        // Fetch an existing record
        if( !empty($id) ){
            $variantConfiguration = Plugin::getInstance()
                ->getVariantConfigurations()
                ->getVariantConfigurationById($id);
        } else {
            $variantConfiguration = new VariantConfigurationModel();
        }

        // Set the type and site ID's
        $variantConfiguration->typeId = $typeId;
        $variantConfiguration->siteId = $siteId ?? $variantConfiguration->siteId;

        // Set the element title
        if( $title = $this->request->getBodyParam('title') ){
            $variantConfiguration->title = $title;
        }

        // Populate safe attributes from POST
        $variantConfiguration->attributes = $this->request->getBodyParams();

        // Set custom field values from the request
        $variantConfiguration->setFieldValuesFromRequest('fields');

        // Now save the element
        $elementsService->saveElement($variantConfiguration);

        return $this->asJson([
            'result' => 'success',
            'data' => $variantConfiguration
        ]);
    }
}
