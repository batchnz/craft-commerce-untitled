<?php

namespace batchnz\craftcommerceuntitled\helpers;

use batchnz\craftcommerceuntitled\Plugin;

/**
 * Helper class for returning plugin route information
 * @since  1.0.0
 */
class Routes
{
    /**
     * Returns an API route from the passed endpoint slug
     * @author Josh Smith <josh@batch.nz>
     * @param  string $endpoint API endpoint
     * @return string
     */
    public static function getApiRoute(string $endpoint): string
    {
        $plugin = Plugin::getInstance();
        return $plugin->id . '/' . $plugin::API_TRIGGER . '/' . $plugin->apiVersion . '/' . $endpoint;
    }

    /**
     * Returns the API controller path for this plugin
     * @author Josh Smith <josh@batch.nz>
     * @param  string $controller The controller name
     * @return string
     */
    public static function getApiController(string $controller): string
    {
        $plugin = Plugin::getInstance();
        return $plugin->id . '/' . $plugin::API_TRIGGER . '/' . $plugin->apiVersion . '/' . $controller;
    }
}
