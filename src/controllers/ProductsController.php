<?php


namespace batchnz\craftcommerceuntitled\controllers;

use batchnz\craftcommerceuntitled\Plugin;
use batchnz\craftcommerceuntitled\enums\ProductVariantType;
use batchnz\craftcommerceuntitled\assetbundles\craftcommerceuntitled\CraftCommerceUntitledAsset as CraftCommerceUntitledBundle;

use Craft;
use craft\base\controller;

use craft\commerce\Plugin as Commerce;
use craft\commerce\controllers\ProductsController as CommerceProductsController;
use craft\commerce\elements\Product;
use craft\commerce\elements\Variant;

use yii\web\Response;

/**
 * Class Products Controller
 */
class ProductsController extends CommerceProductsController
{
    /**
     * @param string $productTypeHandle
     * @param int|null $productId
     * @param string|null $siteHandle
     * @param Product|null $product
     * @return Response
     * @throws Exception
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws SiteNotFoundException
     * @throws InvalidConfigException
     */
    public function actionEditProduct(string $productTypeHandle, int $productId = null, string $siteHandle = null, Product $product = null): Response
    {
        $variables = compact('productTypeHandle', 'productId', 'product');

        if ($siteHandle !== null) {
            $variables['site'] = Craft::$app->getSites()->getSiteByHandle($siteHandle);

            if (!$variables['site']) {
                throw new NotFoundHttpException('Invalid site handle: ' . $siteHandle);
            }
        }

        // Prep site variables
        $this->_prepEditProductVariables($variables);

        // Fetch the product for the current site
        $product = Commerce::getInstance()
            ->getProducts()
            ->getProductById($productId, $variables['site']->id);

        // Run the standard controller if this isn't a configurable variant type
        if( $product->getVariantType() !== ProductVariantType::Configurable ){
            return parent::actionEditProduct($productTypeHandle, $productId, $siteHandle);
        }

        // // ...with a subset of variants
        // $variants = Variant::find()
        //     ->productId($productId)
        //     ->anyStatus()
        //     ->limit(10) // Todo, make configurable
        //     ->siteId($variables['site']->id)
        //     ->all();

        $product->setVariants([]);

        // Load the main plugin scripts
        $this->view->registerAssetBundle(CraftCommerceUntitledBundle::class);
        $this->view->registerJs('Craft.CommerceUntitled.pluginHandle = "'.Plugin::getInstance()->id.'";');
        $this->view->registerJs('Craft.CommerceUntitled.apiVersion = "'.Plugin::getInstance()->apiVersion.'";');
        $this->view->registerJs('new Craft.CommerceUntitled({
            productId: ' . $product->id . ',
            productTypeId: ' . $product->getType()->id . '
        });');

        // Carry on as normal with a preloaded product
        // This prevents the page from crawling to a halt with a large variant set
        return parent::actionEditProduct($productTypeHandle, $productId, $siteHandle, $product);
    }

    /**
     * Prepare controller variables
     * @author Josh Smith <josh@batch.nz>
     * @param  array &$variables
     * @return void
     */
    private function _prepEditProductVariables(array &$variables)
    {
        if (Craft::$app->getIsMultiSite()) {
            // Only use the sites that the user has access to
            $variables['siteIds'] = Craft::$app->getSites()->getEditableSiteIds();
        } else {
            $variables['siteIds'] = [Craft::$app->getSites()->getPrimarySite()->id];
        }

        if (!$variables['siteIds']) {
            throw new ForbiddenHttpException('User not permitted to edit content in any sites supported by this product type');
        }

        if (empty($variables['site'])) {
            $variables['site'] = Craft::$app->getSites()->currentSite;

            if (!in_array($variables['site']->id, $variables['siteIds'], false)) {
                $variables['site'] = Craft::$app->getSites()->getSiteById($variables['siteIds'][0]);
            }

            $site = $variables['site'];
        } else {
            // Make sure they were requesting a valid site
            /** @var Site $site */
            $site = $variables['site'];
            if (!in_array($site->id, $variables['siteIds'], false)) {
                throw new ForbiddenHttpException('User not permitted to edit content in this site');
            }
        }
    }
}
