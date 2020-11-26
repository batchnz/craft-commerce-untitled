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
    public $values;

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['method', 'required'];
        $rules[] = [['method', 'field', 'values'], 'safe'];
        $rules[] = ['method', 'in', 'range' => self::VALID_SETTINGS_METHODS, 'message' => 'Invalid setting "{value}". Expected one of ' . implode(', ', self::VALID_SETTINGS_METHODS) . '.'];

        // Ensure a field handle is passed when the method is "field"
        $rules[] = ['field', function($attr, $params){
            if( $this->method === 'field' && empty($this->$attr) ){
                $this->addError($attr, 'Field handle cannot be blank.');
            }
        }, 'skipOnEmpty' => false];

        // Ensure values are passed when the method is "field"
        $rules[] = ['values', function($attr, $params){
            if( $this->method !== 'field' ) return;
            if( !is_array($this->$attr) ){
                $this->addError($attr, 'Values must be an array.');
            }
        }, 'skipOnEmpty' => false];

        return $rules;
    }
}
