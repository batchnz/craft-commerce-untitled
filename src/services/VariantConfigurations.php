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
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     CraftCommerceUntitled::$plugin->variantConfigurations->exampleService()
     *
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';
        // Check our Plugin's settings for `someAttribute`
        if (CraftCommerceUntitled::$plugin->getSettings()->someAttribute) {
        }

        return $result;
    }
}
