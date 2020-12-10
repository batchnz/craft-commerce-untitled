<?php

namespace batchnz\craftcommerceuntitled\models;

use batchnz\craftcommerceuntitled\elements\VariantConfiguration as VariantConfigurationModel;

use craft\base\Model;
use craft\commerce\elements\Variant;
use craft\commerce\elements\db\VariantQuery;

/**
 * Represents a configuration type
 * stored as settings on a Variant Configuration Element
 */
class VariantConfigurationSetting extends Model
{
    // Constant Values
    public const VALID_SETTINGS_TYPES = ['stock', 'price', 'sku'];
    public const VALID_SETTINGS_METHODS = ['all', 'skip', 'field'];

    public $field;
    public $method;
    public $values;

    /**
     * Returns a variant query to fetch variants that match the configuration settings
     * @author Josh Smith <josh@batch.nz>
     * @return VariantQuery
     */
    public function getVariantQuery(): VariantQuery
    {
        $variantQuery = Variant::find();

        switch ($this->method) {
            // Populates the variant query with field values
            case 'field':
                if( !empty($this->field) && is_array($this->values) ){
                    $variantQuery->{$this->field}(array_keys($this->values));
                }
                break;

            default:
                break;
        }

        return $variantQuery;
    }

    /**
     * Validation Rules
     * @author Josh Smith <josh@batch.nz>
     * @return array
     */
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
