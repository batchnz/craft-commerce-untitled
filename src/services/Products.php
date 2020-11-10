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
