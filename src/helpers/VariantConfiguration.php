<?php

namespace batchnz\craftcommerceuntitled\helpers;

use batchnz\craftcommerceuntitled\Plugin;
use batchnz\craftcommerceuntitled\elements\VariantConfiguration as VariantConfigurationModel;

use Craft;
use craft\web\Request;
use yii\base\Exception;
use yii\web\NotFoundHttpException;

/**
 * Variant Configuration Helper Class
 * @author  Josh Smith <josh@batch.nz>
 * @since  1.0.0
 */
class VariantConfiguration
{
    /**
     * Instantiates the variant configuration specified by the post data.
     * Designed to be called from an API controller action handler
     *
     * @param Request|null $request
     * @return VariantConfigurationModel
     * @throws NotFoundHttpException
     */
    public static function variantConfigurationFromPost(Request $request = null): VariantConfigurationModel
    {
        if ($request === null) {
            $request = Craft::$app->getRequest();
        }

        $variantConfigurationId = $request->getBodyParam('id');
        $siteId = $request->getBodyParam('siteId');

        if ($variantConfigurationId) {
            try {
                $variantConfiguration = Plugin::getInstance()
                    ->getVariantConfigurations()
                    ->getVariantConfigurationById($variantConfigurationId);

                if (!$variantConfiguration) {
                    throw new Exception();
                }
            } catch (\TypeError | Exception $e) {
                throw new NotFoundHttpException(
                    Craft::t(
                        Plugin::getInstance()->id,
                        'No variant configuration found with the ID “{id}”',
                        ['id' => $variantConfigurationId]
                    )
                );
            }
        } else {
            $variantConfiguration = new VariantConfigurationModel();
            $variantConfiguration->typeId = $request->getBodyParam('typeId');
            $variantConfiguration->productId = $request->getBodyParam('productId');
            $variantConfiguration->siteId = $siteId ?? $variantConfiguration->siteId;
        }

        return $variantConfiguration;
    }

    /**
     * Populates a variant configuration from the post data.
     *
     * @param VariantConfigurationModel|null $variantConfiguration
     * @param Request|null $request
     * @return VariantConfigurationModel
     * @throws NotFoundHttpException
     */
    public static function populateVariantConfigurationFromPost(VariantConfigurationModel $variantConfiguration = null, Request $request = null): VariantConfigurationModel
    {
        if ($request === null) {
            $request = Craft::$app->getRequest();
        }

        if ($variantConfiguration === null) {
            $variantConfiguration = static::variantConfigurationFromPost($request);
        }

        // Set the element title
        if ($title = $request->getBodyParam('title')) {
            $variantConfiguration->title = $title;
        }

        // Populate safe attributes from POST
        $variantConfiguration->attributes = $request->getBodyParams();

        // Set custom field values from the request
        $variantConfiguration->setFieldValuesFromRequest('values');

        // Set settings from the request
        $variantConfiguration->setSettingsFromRequest('settings');

        return $variantConfiguration;
    }
}
