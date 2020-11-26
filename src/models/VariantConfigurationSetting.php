<?php

namespace batchnz\craftcommerceuntitled\models;

use batchnz\craftcommerceuntitled\elements\VariantConfiguration as VariantConfigurationModel;

use craft\base\Model;

/**
 * Represents a configuration type
 * stored as settings on a Variant Configuration Element
 */
class VariantConfigurationSetting extends Model
{
    // Constant Values
    public const VALID_SETTINGS_TYPES = ['stock', 'price', 'images'];
    public const VALID_SETTINGS_METHODS = ['all', 'skip', 'field'];

    public $field;
    public $method;
    public $value;

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['method', 'required'];
        $rules[] = ['method', 'in', 'range' => self::VALID_SETTINGS_METHODS, 'message' => 'Invalid setting "{value}". Expected one of ' . implode(', ', self::VALID_SETTINGS_METHODS) . '.'];
        return $rules;
    }
}
