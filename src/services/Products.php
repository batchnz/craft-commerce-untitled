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
use batchnz\craftcommerceuntitled\enums\ProductVariantType;
use batchnz\craftcommerceuntitled\models\commerce\ConfigurableProductType;
use batchnz\craftcommerceuntitled\records\Product;

use Craft;
use craft\base\Component;
use craft\events\ModelEvent;
use craft\events\TemplateEvent;
use craft\web\View;

use craft\commerce\elements\Product as CommerceProduct;

/**
 * Products Service
 *
 * @author    Josh Smith
 * @package   CraftCommerceUntitled
 * @since     1.0.0
 */
class Products extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Handles the before render page template event for product CP page templates
     * We use this event to inject additional variables and alter the view to show the configurable variant html
     * @author Josh Smith <josh@batch.nz>
     * @param  TemplateEvent $e
     * @return void
     */
    public function handleProductBeforeRenderPageTemplateEvent(TemplateEvent $e)
    {
        // Only run this method on the _edit template
        if( $e->template !== 'commerce/products/_edit' ) return;

        $view = Craft::$app->getView();
        $scripts = $view->js[View::POS_READY];

        $product = $e->variables['product'];
        $productType = $e->variables['productType'];

        // Nothing to do for standard product variant types...
        if( !($product->getVariantType() === ProductVariantType::Configurable) ) return;

        // Create a new instance of the plugin's ConfigurableProductType model
        $configurableProductType = new ConfigurableProductType($productType);

        // Create the configurable tab menu and fields HTML
        $form = $configurableProductType->getProductFieldLayout()->createForm($product);

        $e->variables['tabs'] = $form->getTabMenu();
        $e->variables['fieldsHtml'] = $form->render();

        // Hack alert!
        // Calling `$configurableProductType->getProductFieldLayout()->createForm($product);` will duplicate
        // any scripts registered during the fieldlayoutelement HTML generation. As we're simply replacing
        // Variant Fields with our own extended version, we can reset the scripts array to the original.
        // This is required as we can't override the `_prepEditProductVariables()` method in the products controller as it's private.
        $view->js[View::POS_READY] = $scripts;
    }

    /**
     * Handles a Commerce Product element save event and updates the plugin's product record
     * @author Josh Smith <josh@batch.nz>
     * @param  ModelEvent $e
     * @return void
     */
    public function handleProductSaveEvent(ModelEvent $e)
    {
        $commerceProduct = $e->sender;
        $variantType = Craft::$app->getRequest()->getBodyParam('variantType');

        // Attempt to fetch an existing record
        $product = Product::findOne($commerceProduct->id);

        // ...And create a new one if we couldn't find one
        if( empty($product) ){
            $product = new Product();
            $product->id = $commerceProduct->id;
        }

        // Save the variant type if it exists in the request
        if( !empty($variantType) ){
            $product->variantType = $variantType;
        }

        $product->save();
    }
}
