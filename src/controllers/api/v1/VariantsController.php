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

use craft\base\Element;
use craft\commerce\Plugin as Commerce;
use craft\commerce\elements\Variant;
use craft\commerce\helpers\Currency as CurrencyHelper;

use yii\rest\Controller;
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
        if (empty($product)) {
            throw new NotFoundHttpException();
        }

        // Get the main currency
        $currency = Commerce::getInstance()->getPaymentCurrencies()->getPrimaryPaymentCurrency();
        if (empty($currency)) {
            throw new NotFoundHttpException();
        }

        // Fetch the fields on the variant field layout
        $variantFieldLayout = $product->getType()->getVariantFieldLayout();
        $fields = array_column($variantFieldLayout->getCustomFields(), 'handle');

        // Fetch variants with eager loaded fields
        $variants = Variant::find()
            ->product($productId)
            ->with($fields)
        ->all();

        $data = [];
        foreach ($variants as $variant) {
            $data[] = [
                ...[
                    $variant->sku,
                    $variant->stock,
                    CurrencyHelper::formatAsCurrency($variant->price, $currency),
                    $variant->minQty,
                    $variant->maxQty,
                    ...($product->getType()->hasDimensions ? [
                        $variant->weight,
                        $variant->length,
                        $variant->width,
                        $variant->height,
                    ] : []),
                ],
                ...$this->_getVariantFieldValues($variant, $fields)
            ];
        }

        return $this->asJson([
            'data' => $data,
        ]);
    }

    /**
     * Helper function to return field values for a variant
     * @author Josh Smith <josh@batch.nz>
     * @param  Variant $variant
     * @param  array $fields
     * @return array
     */
    private function _getVariantFieldValues(Variant $variant, array $fields): array
    {
        return array_map(function ($field) use ($variant) {
            // Return element titles or just the value
            $values = array_map(function ($value) {
                return $value instanceof Element ? $value->title : $value;
            }, $variant->$field->toArray());

            return implode(', ', $values);
        }, $fields) ?? [];
    }
}
