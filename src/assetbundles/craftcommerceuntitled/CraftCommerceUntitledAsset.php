<?php
/**
 * Craft Commerce Untitled plugin for Craft CMS 3.x
 *
 * Manage commerce variants by their field types and values
 *
 * @link      https://www.batch.nz
 * @copyright Copyright (c) 2020 Josh Smith
 */

namespace batchnz\craftcommerceuntitled\assetbundles\craftcommerceuntitled;

use batchnz\craftcommerceuntitled\Plugin;

use nystudio107\twigpack\helpers\Manifest as ManifestHelper;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;
use craft\web\View;

use yii\web\JqueryAsset;

/**
 * CraftCommerceUntitledAsset AssetBundle
 *
 * AssetBundle represents a collection of asset files, such as CSS, JS, images.
 *
 * Each asset bundle has a unique name that globally identifies it among all asset bundles used in an application.
 * The name is the [fully qualified class name](http://php.net/manual/en/language.namespaces.rules.php)
 * of the class representing it.
 *
 * An asset bundle can depend on other asset bundles. When registering an asset bundle
 * with a view, all its dependent asset bundles will be automatically registered.
 *
 * http://www.yiiframework.com/doc-2.0/guide-structure-assets.html
 *
 * @author    Josh Smith
 * @package   CraftCommerceUntitled
 * @since     1.0.0
 */
class CraftCommerceUntitledAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        $twigpackConfig = $this->getConfig('twigpack');

        // define the path that your publishable resources live
        $this->sourcePath = "@batchnz/craftcommerceuntitled/assetbundles/craftcommerceuntitled/dist";

        // define the dependencies
        $this->depends = [
            CpAsset::class,
            JqueryAsset::class,
        ];

        // define the relative path to CSS/JS files that should be registered with the page
        // when this asset bundle is registered
        $this->js = [
            'libs/DataTables/datatables.min.js'
        ];

        // Load the correct webpack files
        if( Craft::$app->getConfig()->general->devMode && $this->hasDevServer() ){
            $this->js[] = ManifestHelper::getModule($twigpackConfig, 'app.js');
        // Use production files
        } else {
            $this->js[] = 'js/app.js';
            $this->js[] = 'js/runtime.js';
            $this->js[] = 'js/vendors.js';
            $this->css[] = 'css/app.css';
        }

        parent::init();
    }

     /**
     * @inheritdoc
     */
    public function registerAssetFiles($view)
    {
        parent::registerAssetFiles($view);

        if ($view instanceof View) {
            $view->registerTranslations(Plugin::getInstance()->id, [
                'price',
                'stock',
                'sku',
            ]);
        }
    }

    /**
     * Returns whether the dev server is up and running
     * @author Josh Smith <josh@batch.nz>
     * @return boolean
     */
    protected function hasDevServer()
    {
        $arrContextOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ];
        $devServerHost = getenv('DEVSERVER_PUBLIC');

        try {
            $contents = file_get_contents("$devServerHost/manifest.json", false, stream_context_create($arrContextOptions));
        } catch (\Exception $exception) {
            return false;
        }

        return !empty($contents);
    }

    /**
     * Resolves a plugin config file
     * @author Josh Smith <josh@batch.nz>
     * @param  string $module
     * @return array
     */
    protected function getConfig(string $module): array
    {
        $configService = Craft::$app->getConfig();

        $origConfigDir = $configService->configDir;

        $configService->configDir = Craft::getAlias('@batchnz/craftcommerceuntitled/config');
        $config = Craft::$app->config->getConfigFromFile($module);

        $configService->configDir = $origConfigDir;

        return $config ?? [];
    }
}
