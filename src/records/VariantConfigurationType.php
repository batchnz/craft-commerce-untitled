<?php
/**
 * Craft Commerce Untitled plugin for Craft CMS 3.x
 *
 * Manage commerce variants by their field types and values
 *
 * @link      https://www.batch.nz
 * @copyright Copyright (c) 2020 Josh Smith
 */

namespace batchnz\craftcommerceuntitled\records;

use batchnz\craftcommerceuntitled\Plugin;

use Craft;
use craft\db\ActiveRecord;

/**
 * VariantConfiguration Record
 *
 * @author    Josh Smith
 * @package   CraftCommerceUntitled
 * @since     1.0.0
 */
class VariantConfigurationType extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

     /**
      * The table name for this AR class
      * @author Josh Smith <josh@batch.nz>
      * @return string
      */
    public static function tableName()
    {
        return '{{%commerce_untitled_variantconfiguration_types}}';
    }
}
