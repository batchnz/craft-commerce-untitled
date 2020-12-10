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

use craft\commerce\Plugin as Commerce;
use craft\commerce\elements\Variant;

use Craft;
use craft\elements\db\Element;

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
class VariantsController extends Controller
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
        $productId = $this->request->getQueryParam('productId');
        $offset = $this->request->getBodyParam('start');
        $limit = $this->request->getBodyParam('length');
        $search = $this->request->getBodyParam('search');

        // Fetch product
        $product = Commerce::getInstance()->getProducts()->getProductById($productId);
        if( empty($product) ) throw new NotFoundHttpException();

        // Fetch the fields on the variant field layout
        $variantFieldLayout = $product->getType()->getVariantFieldLayout();
        $fields = array_column($variantFieldLayout->getFields(), 'handle');

        // Fetch variants with eager loaded fields
        $variants = Variant::find()
            ->product($productId)
            ->with($fields)
        ->all();

        $data = [];
        foreach ($variants as $variant) {
            $data[] = [
                ...[$variant->sku, $variant->stock, $variant->price],
                ...array_map(function($field) use($variant) {
                    return ($variant instanceof Element) ?
                        $variant->{$field}[0]->title :
                        $variant->{$field};
                }, $fields)
            ];
        }

        return $this->asJson([
            'data' => $data,
        ]);
    }
}
