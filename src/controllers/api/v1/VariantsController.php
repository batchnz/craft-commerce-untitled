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

        $recordsTotal = Variant::find()->product($productId)->count();
        $recordsFilteredQuery = Variant::find()->product($productId);

        $variantsQuery = Variant::find()
            ->product($productId)
            ->limit($limit);

        if( $offset ){
            $variantsQuery->offset($offset);
        }

        if( $limit ){
            $variantsQuery->limit($limit);
        }

        if( !empty($search['value']) ){
            $recordsFilteredQuery->search($search['value']);
            $variantsQuery->search($search['value']);
        }

        $recordsFiltered = $recordsFilteredQuery->count();
        $variants = $variantsQuery->all();

        // $product = Commerce::getInstance()->getProducts()->getProductById($productId);
        // foreach ($product-> as $key => $value) {
        //     # code...
        // }

        $data = [];
        foreach ($variants as $variant) {
            $data[] = [
                $variant->sku,
                $variant->stock,
                $variant->price,
                $variant->paintColour,
                $variant->paintSheen,
                $variant->size,
            ];
        }

        return $this->asJson([
            'data' => $data,
            'draw' => (int) $this->request->getBodyParam('draw'),
            'recordsTotal' => (int) $recordsTotal,
            'recordsFiltered' => (int) $recordsFiltered
        ]);
        // // Initialise a blank variant configuration query
        // $variantConfigurationsQuery = VariantConfigurationModel::find();

        // if( $id = $this->request->getQueryParam('id') ){
        //     $variantConfigurationsQuery->id($id);
        // }

        // // Filter on product Ids
        // if( $productId = $this->request->getQueryParam('productId') ){
        //     $variantConfigurationsQuery->productId($productId);
        // }

        // // Fetch all matching variant configurations
        // $variantConfigurations = $variantConfigurationsQuery->all();

        // return $this->asJson([
        //     'result' => 'success',
        //     'data' => $variantConfigurations
        // ]);
    }
}
