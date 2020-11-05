<?php
/**
 * Craft Commerce Untitled plugin for Craft CMS 3.x
 *
 * Manage commerce variants by their field types and values
 *
 * @link      https://www.batch.nz
 * @copyright Copyright (c) 2020 Josh Smith
 */

namespace batchnz\craftcommerceuntitled;

use batchnz\craftcommerceuntitled\behaviors\ConfigurableProductBehavior;
use batchnz\craftcommerceuntitled\elements\VariantConfiguration as VariantConfigurationElement;
use batchnz\craftcommerceuntitled\enums\ProductVariantType;
use batchnz\craftcommerceuntitled\fields\VariantsTable as VariantsTableField;
use batchnz\craftcommerceuntitled\models\Settings;
use batchnz\craftcommerceuntitled\services\Products as ProductsService;
use batchnz\craftcommerceuntitled\services\VariantConfigurations as VariantConfigurationsService;

use Craft;
use craft\base\Plugin as CraftPlugin;
use craft\base\Element;
use craft\events\DefineBehaviorsEvent;
use craft\events\ModelEvent;
use craft\events\PluginEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\TemplateEvent;
use craft\services\Elements;
use craft\services\Fields;
use craft\services\Plugins;
use craft\web\View;
use craft\web\UrlManager;

use craft\commerce\elements\Product as CommerceProduct;

use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    Josh Smith
 * @package   CraftCommerceUntitled
 * @since     1.0.0
 *
 * @property  VariantConfigurationsService $variantConfigurations
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class Plugin extends CraftPlugin
{
    // Constants
    // =========================================================================
    public const PLUGIN_HANDLE = 'craft-commerce-untitled';

    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * CraftCommerceUntitled::$plugin
     *
     * @var CraftCommerceUntitled
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public $hasCpSettings = false;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * CraftCommerceUntitled::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Bootstrap plugin
        $this->_registerComponents();
        $this->_registerEvents();
        $this->_registerHooks();

        /**
         * Logging in Craft involves using one of the following methods:
         *
         * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
         * Craft::info(): record a message that conveys some useful information.
         * Craft::warning(): record a warning message that indicates something unexpected has happened.
         * Craft::error(): record a fatal error that should be investigated as soon as possible.
         *
         * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
         *
         * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
         * the category to the method (prefixed with the fully qualified class name) where the constant appears.
         *
         * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
         * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
         *
         * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
         */
        Craft::info(
            Craft::t(
                self::PLUGIN_HANDLE,
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    /**
     * Returns the products service
     * @author Josh Smith <josh@batch.nz>
     * @return batchnz\craftcommerceuntitled\services\Products
     */
    public function getProducts()
    {
        return $this->get('products');
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            self::PLUGIN_HANDLE . '/settings',
            ['settings' => $this->getSettings()]
        );
    }

    // Private Methods
    // =========================================================================

    /**
     * Register plugin services
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    private function _registerComponents()
    {
        Craft::$app->setComponents([
            'products' => ProductsService::class
        ]);
    }

    /**
     * Register plugin event handlers
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    private function _registerEvents()
    {
         // Register our CP routes
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['cpActionTrigger1'] = self::PLUGIN_HANDLE . '/variant-configuration/do-something';
            }
        );

        // Register our elements
        Event::on(Elements::class, Elements::EVENT_REGISTER_ELEMENT_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = VariantConfigurationElement::class;
            }
        );

        // Register our fields
        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = VariantsTableField::class;
            }
        );

        // Register plugin behaviors
        Event::on(
            CommerceProduct::class, CommerceProduct::EVENT_DEFINE_BEHAVIORS, function(DefineBehaviorsEvent $event) {
                $event->sender->attachBehaviors([
                    ConfigurableProductBehavior::class
                ]);
            }
        );

        // Handle Commerce Product element save events
        Event::on(CommerceProduct::class, Element::EVENT_AFTER_SAVE, function(ModelEvent $e) {
            $this->getProducts()->handleProductSaveEvent($e);
        });

        // Handle Craft before page load event
        Event::on(View::class, View::EVENT_BEFORE_RENDER_PAGE_TEMPLATE, function(TemplateEvent $e){
            if( strpos($e->template, 'commerce/products/') !== false ){
                $this->getProducts()->handleProductBeforeRenderPageTemplateEvent($e);
            }
        });
    }

    /**
     * Register plugin template hooks
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    private function _registerHooks()
    {
        Craft::$app->view->hook('cp.commerce.product.edit.details', function(array &$context) {
            // Define an array of product variant types
            $context['productVariantTypes'] = [
                ProductVariantType::Standard => 'Standard',
                ProductVariantType::Configurable => 'Configurable'
            ];

            return Craft::$app->view->renderTemplate(self::PLUGIN_HANDLE . '/_components/hooks/cp-commerce-product-edit-details', $context);
        });
    }
}
