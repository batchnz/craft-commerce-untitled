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
use batchnz\craftcommerceuntitled\behaviors\NormalizeBaseFieldValuesBehavior;
use batchnz\craftcommerceuntitled\behaviors\NormalizeBaseOptionsFieldValuesBehavior;
use batchnz\craftcommerceuntitled\behaviors\NormalizeBaseRelationFieldValuesBehavior;
use batchnz\craftcommerceuntitled\elements\VariantConfiguration as VariantConfigurationElement;
use batchnz\craftcommerceuntitled\enums\ProductVariantType;
use batchnz\craftcommerceuntitled\fields\VariantsTable as VariantsTableField;
use batchnz\craftcommerceuntitled\helpers\Routes as RoutesHelper;
use batchnz\craftcommerceuntitled\models\Settings;
use batchnz\craftcommerceuntitled\services\Fields as FieldsService;
use batchnz\craftcommerceuntitled\services\Products as ProductsService;
use batchnz\craftcommerceuntitled\services\VariantConfigurations as VariantConfigurationsService;
use batchnz\craftcommerceuntitled\services\VariantConfigurationTypes as VariantConfigurationTypesService;

use Craft;
use craft\base\Plugin as CraftPlugin;
use craft\base\Element;
use craft\base\Field;
use craft\events\DefineBehaviorsEvent;
use craft\events\ModelEvent;
use craft\events\PluginEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\TemplateEvent;
use craft\fields\BaseOptionsField;
use craft\fields\BaseRelationField;
use craft\services\Elements;
use craft\services\Fields;
use craft\services\Plugins;
use craft\web\Application;
use craft\web\View;
use craft\web\UrlManager;

use craft\commerce\Plugin as Commerce;
use craft\commerce\models\ProductType;
use craft\commerce\elements\Product as CommerceProduct;
use craft\commerce\events\ProductTypeEvent;
use craft\commerce\services\ProductTypes;

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
    /**
     * Defines a variable for the API slug
     * @var string
     */
    public const API_TRIGGER = 'api';

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
    public $schemaVersion = '1.0.1';

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

    /**
     * Define the current API version
     * @var string
     */
    public $apiVersion = 'v1';

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
        $this->_registerBehaviors();
        $this->_registerEvents();
        $this->_registerHooks();

        Craft::info(
            Craft::t(
                $this->id,
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    /**
     * Returns the fields service
     * @author Josh Smith <josh@batch.nz>
     * @return batchnz\craftcommerceuntitled\services\Fields
     */
    public function getFields()
    {
        return $this->get('fields');
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

    /**
     * Returns the variant configurations service
     * @author Josh Smith <josh@batch.nz>
     * @return batchnz\craftcommerceuntitled\services\VariantConfigurations
     */
    public function getVariantConfigurations()
    {
        return $this->get('variantConfigurations');
    }

    /**
     * Returns the variant configuration types service
     * @author Josh Smith <josh@batch.nz>
     * @return batchnz\craftcommerceuntitled\services\VariantConfigurationTypes
     */
    public function getVariantConfigurationTypes()
    {
        return $this->get('variantConfigurationTypes');
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
            $this->id . '/settings',
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
        $this->setComponents([
            'fields' => FieldsService::class,
            'products' => ProductsService::class,
            'variantConfigurations' => VariantConfigurationsService::class,
            'variantConfigurationTypes' => VariantConfigurationTypesService::class,
        ]);
    }

    /**
     * Registers behaviors used by the plugin
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    private function _registerBehaviors()
    {
        // Craft Base Field Behaviors
        Event::on(
            Field::class, Field::EVENT_DEFINE_BEHAVIORS,
            function(DefineBehaviorsEvent $event) {
                $event->sender->attachBehaviors([
                    NormalizeBaseFieldValuesBehavior::class
                ]);
            }
        );

        // Craft Base Relation Field Behaviors
        Event::on(
            BaseRelationField::class, BaseRelationField::EVENT_DEFINE_BEHAVIORS,
            function(DefineBehaviorsEvent $event) {
                $event->sender->attachBehaviors([
                    NormalizeBaseRelationFieldValuesBehavior::class
                ]);
            }
        );

        // Craft Base Options Field Behaviors
        Event::on(
            BaseOptionsField::class, BaseOptionsField::EVENT_DEFINE_BEHAVIORS,
            function(DefineBehaviorsEvent $event) {
                $event->sender->attachBehaviors([
                    NormalizeBaseOptionsFieldValuesBehavior::class
                ]);
            }
        );

        // Craft Commerce Product Behaviors
        Event::on(
            CommerceProduct::class, CommerceProduct::EVENT_DEFINE_BEHAVIORS,
            function(DefineBehaviorsEvent $event) {
                $event->sender->attachBehaviors([
                    ConfigurableProductBehavior::class
                ]);
            }
        );
    }

    /**
     * Register plugin event handlers
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    private function _registerEvents()
    {
         // Register API routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                // Products Controller
                $event->rules[] = [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        RoutesHelper::getApiRoute('products') => RoutesHelper::getApiController('products')
                    ],
                    'extraPatterns' => [
                        'POST <id:\d+>/types' => 'save-types'
                    ]
                ];

                // Variants Controller
                $event->rules[] = [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        RoutesHelper::getApiRoute('variants') => RoutesHelper::getApiController('variants')
                    ],
                ];

                // Variant Configurations Controller
                $event->rules[] = [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        RoutesHelper::getApiRoute('variant-configurations') => RoutesHelper::getApiController('variant-configurations')
                    ],
                    'extraPatterns' => [
                        'POST' => 'save',
                        'PATCH <id:\d+>/fields' => 'save-fields',
                        'PATCH <id:\d+>/values' => 'save-values',
                        'PATCH <id:\d+>/settings' => 'save-settings',
                        'POST <id:\d+>/variants/generate' => 'generate-variants',
                        'POST <id:\d+>/variants/delete' => 'delete-variants'
                    ],
                ];

                // Variant Configuration Types Controller
                $event->rules[] = [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        RoutesHelper::getApiRoute('variant-configuration-types') => RoutesHelper::getApiController('variant-configuration-types')
                    ],
                    'except' => ['delete', 'create', 'update'],
                    'extraPatterns' => [
                        'GET fields' => 'fields'
                    ],
                ];
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

        /**
         * Hack alert!
         * We can't prevent craft commerce from attempting to save variants via the standard controller
         * so we're setting the variants body param to an empty array and injecting the current set of
         * variants on to the product element within the Product::EVENT_BEFORE_SAVE event.
         */
        Craft::$app->on(Application::EVENT_INIT, function() {
            if( ! Craft::$app->getRequest()->getIsCpRequest() ) return;

            $actionSegments = $this->request->getActionSegments();
            if( empty($actionSegments) ) return;

            if(
                count($actionSegments) === 3 &&
                $actionSegments[0] === 'commerce' &&
                $actionSegments[1] === 'products' &&
                $actionSegments[2] === 'save-product'
            ){
                $bodyParams = $this->request->getBodyParams();
                if(isset($bodyParams['variantType']) && $bodyParams['variantType'] === 'configurable') {
                    $newBodyParams = array_merge($bodyParams, ['variants' => []]);
                    $this->request->setBodyParams($newBodyParams);
                }
            }
        });

        // Handle Commerce Product Types After Save Event
        Event::on(
            ProductTypes::class,
            ProductTypes::EVENT_AFTER_SAVE_PRODUCTTYPE,
            function(ProductTypeEvent $e) {
                $this->getVariantConfigurationTypes()->handleProductTypeAfterSaveEvent($e);
            }
        );

        // Handle Craft before page load event
        // We use this event to overwrite the variant tabs with our own custom HTML
        Event::on(View::class, View::EVENT_BEFORE_RENDER_PAGE_TEMPLATE, function(TemplateEvent $e){
            if( strpos($e->template, 'commerce/products/') !== false ){
                $this->getProducts()->handleProductBeforeRenderPageTemplateEvent($e);
            }
        });

        // Handle after plugins loaded event
        // We use this event to re-route product edit routes to our custom controller
        // This allows us to limit the number of variants loaded against a product
        Event::on(Plugins::class, Plugins::EVENT_AFTER_LOAD_PLUGINS, function(Event $event){
            if( ! Craft::$app->getRequest()->getIsCpRequest() ) return;

            // Swap the commerce products service with our own
            Commerce::getInstance()->set('products', [
                'class' => ProductsService::class
            ]);

            $urlManager = Craft::$app->getUrlManager();
            $urlManager->addRules([
                'commerce/products/<productTypeHandle:{handle}>/new/<siteHandle:{handle}>' => $this->id . '/products/edit-product',
                'commerce/products/<productTypeHandle:{handle}>/<productId:\d+><slug:(?:-[^\/]*)?>' => $this->id . '/products/edit-product',
                'commerce/products/<productTypeHandle:{handle}>/<productId:\d+><slug:(?:-[^\/]*)?>/<siteHandle:{handle}>' => $this->id . '/products/edit-product'
            ], false);
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

            return Craft::$app->view->renderTemplate($this->id . '/_components/hooks/cp-commerce-product-edit-details', $context);
        });
    }
}
