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

use craft\commerce\Plugin as Commerce;
use craft\commerce\elements\Variant;

use Craft;

use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

/**
 * Variant Configurations Controller
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Josh Smith
 * @package   CraftCommerceUntitled
 * @since     1.0.0
 */
class ProductsController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * POST endpoint for saving the product type
     * @author Josh Smith <josh@batch.nz>
     * @return JSON
     */
    public function actionSaveTypes($id)
    {
        $product = Commerce::getInstance()->getProducts()->getProductById($id);
        if (empty($product)) {
            throw new NotFoundHttpException('Product not found');
        }

        $variantType = Craft::$app->getRequest()->getBodyParam('variantType');
        if (empty($variantType)) {
            throw new BadRequestHttpException('Variant Type is missing');
        }

        $result = Plugin::getInstance()->getProducts()->saveProductVariantType($product, $variantType);

        return $this->asJson([
            'result' => 'success'
        ]);
    }
}
