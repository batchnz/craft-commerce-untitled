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
use batchnz\craftcommerceuntitled\elements\VariantConfiguration;

use Craft;
use craft\base\Component;

/**
 * VariantConfigurations Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Josh Smith
 * @package   CraftCommerceUntitled
 * @since     1.0.0
 */
class VariantConfigurations extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Get a configuration by ID.
     *
     * @param int $id
     * @param int $siteId
     * @return VariantConfiguration|null
     */
    public function getVariantConfigurationById(int $id, $siteId = null)
    {
        /** @var VariantConfiguration $variantConfiguration */
        $variantConfiguration = Craft::$app->getElements()->getElementById($id, VariantConfiguration::class, $siteId);

        return $variantConfiguration;
    }

}
