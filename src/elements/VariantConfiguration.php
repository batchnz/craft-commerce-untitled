<?php
/**
 * Craft Commerce Untitled plugin for Craft CMS 3.x
 *
 * Manage commerce variants by their field types and values
 *
 * @link      https://www.batch.nz
 * @copyright Copyright (c) 2020 Josh Smith
 */

namespace batchnz\craftcommerceuntitled\elements;

use batchnz\craftcommerceuntitled\Plugin;
use batchnz\craftcommerceuntitled\elements\db\VariantConfigurationQuery;
use batchnz\craftcommerceuntitled\helpers\ArrayHelper;
use batchnz\craftcommerceuntitled\models\VariantConfigurationSetting;
use batchnz\craftcommerceuntitled\records\VariantConfigurationType;
use batchnz\craftcommerceuntitled\records\VariantConfiguration as VariantConfigurationRecord;

use Craft;
use craft\base\Element;
use craft\base\Field;
use craft\elements\db\ElementQuery;
use craft\elements\db\ElementQueryInterface;
use craft\fields\BaseRelationField;
use craft\fields\Categories as CategoriesField;
use craft\helpers\Json;
use craft\validators\ArrayValidator;

use craft\commerce\Plugin as Commerce;
use craft\commerce\elements\Product;

use yii\base\InvalidConfigException;

/**
 * VariantConfiguration Element
 *
 * Element is the base class for classes representing elements in terms of objects.
 *
 * @property FieldLayout|null      $fieldLayout           The field layout used by this element
 * @property array                 $htmlAttributes        Any attributes that should be included in the element’s DOM representation in the Control Panel
 * @property int[]                 $supportedSiteIds      The site IDs this element is available in
 * @property string|null           $uriFormat             The URI format used to generate this element’s URL
 * @property string|null           $url                   The element’s full URL
 * @property \Twig_Markup|null     $link                  An anchor pre-filled with this element’s URL and title
 * @property string|null           $ref                   The reference string to this element
 * @property string                $indexHtml             The element index HTML
 * @property bool                  $isEditable            Whether the current user can edit the element
 * @property string|null           $cpEditUrl             The element’s CP edit URL
 * @property string|null           $thumbUrl              The URL to the element’s thumbnail, if there is one
 * @property string|null           $iconUrl               The URL to the element’s icon image, if there is one
 * @property string|null           $status                The element’s status
 * @property Element               $next                  The next element relative to this one, from a given set of criteria
 * @property Element               $prev                  The previous element relative to this one, from a given set of criteria
 * @property Element               $parent                The element’s parent
 * @property mixed                 $route                 The route that should be used when the element’s URI is requested
 * @property int|null              $structureId           The ID of the structure that the element is associated with, if any
 * @property ElementQueryInterface $ancestors             The element’s ancestors
 * @property ElementQueryInterface $descendants           The element’s descendants
 * @property ElementQueryInterface $children              The element’s children
 * @property ElementQueryInterface $siblings              All of the element’s siblings
 * @property Element               $prevSibling           The element’s previous sibling
 * @property Element               $nextSibling           The element’s next sibling
 * @property bool                  $hasDescendants        Whether the element has descendants
 * @property int                   $totalDescendants      The total number of descendants that the element has
 * @property string                $title                 The element’s title
 * @property string|null           $serializedFieldValues Array of the element’s serialized custom field values, indexed by their handles
 * @property array                 $fieldParamNamespace   The namespace used by custom field params on the request
 * @property string                $contentTable          The name of the table this element’s content is stored in
 * @property string                $fieldColumnPrefix     The field column prefix this element’s content uses
 * @property string                $fieldContext          The field context this element’s content uses
 *
 * http://pixelandtonic.com/blog/craft-element-types
 *
 * @author    Josh Smith
 * @package   CraftCommerceUntitled
 * @since     1.0.0
 */
class VariantConfiguration extends Element
{
    // Scenarios
    public const SCENARIO_SET_FIELDS = 'setFields';
    public const SCENARIO_SET_SETTINGS = 'setSettings';
    public const SCENARIO_SET_VALUES = 'setValues';

    // Public Properties
    // =========================================================================

    /**
     * ID of the associated product
     *
     * @var string
     */
    public $productId;

    /**
     * ID of the associated variant configuration type
     *
     * @var string
     */
    public $typeId;

    /**
     * Variant configuration fields
     *
     * @var string
     */
    public $fields;

    /**
     * Variant configuration settings
     *
     * @var array
     */
    public $settings;

    // Private Properties
    // =========================================================================

    /**
     * @var array|null Record of the fields whose values have already been normalized
     */
    private $_normalizedFieldValues;

    /**
     * Cache for the element settings
     * @var array
     */
    private $_settings;

    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('craft-commerce-untitled', 'VariantConfiguration');
    }

    /**
     * Returns whether elements of this type will be storing any data in the `content`
     * table (tiles or custom fields).
     *
     * @return bool Whether elements of this type will be storing any data in the `content` table.
     */
    public static function hasContent(): bool
    {
        return true;
    }

    /**
     * Returns whether elements of this type have traditional titles.
     *
     * @return bool Whether elements of this type have traditional titles.
     */
    public static function hasTitles(): bool
    {
        return true;
    }

    /**
     * Returns whether elements of this type have statuses.
     *
     * If this returns `true`, the element index template will show a Status menu
     * by default, and your elements will get status indicator icons next to them.
     *
     * Use [[statuses()]] to customize which statuses the elements might have.
     *
     * @return bool Whether elements of this type have statuses.
     * @see statuses()
     */
    public static function isLocalized(): bool
    {
        return true;
    }

    /**
     * Creates an [[ElementQueryInterface]] instance for query purpose.
     *
     * The returned [[ElementQueryInterface]] instance can be further customized by calling
     * methods defined in [[ElementQueryInterface]] before `one()` or `all()` is called to return
     * populated [[ElementInterface]] instances. For example,
     *
     * ```php
     * // Find the entry whose ID is 5
     * $entry = Entry::find()->id(5)->one();
     *
     * // Find all assets and order them by their filename:
     * $assets = Asset::find()
     *     ->orderBy('filename')
     *     ->all();
     * ```
     *
     * If you want to define custom criteria parameters for your elements, you can do so by overriding
     * this method and returning a custom query class. For example,
     *
     * ```php
     * class Product extends Element
     * {
     *     public static function find()
     *     {
     *         // use ProductQuery instead of the default ElementQuery
     *         return new ProductQuery(get_called_class());
     *     }
     * }
     * ```
     *
     * You can also set default criteria parameters on the ElementQuery if you don’t have a need for
     * a custom query class. For example,
     *
     * ```php
     * class Customer extends ActiveRecord
     * {
     *     public static function find()
     *     {
     *         return parent::find()->limit(50);
     *     }
     * }
     * ```
     *
     * @return ElementQueryInterface The newly created [[ElementQueryInterface]] instance.
     */
    public static function find(): ElementQueryInterface
    {
        return new VariantConfigurationQuery(static::class);
    }

    /**
     * Defines the sources that elements of this type may belong to.
     *
     * @param string|null $context The context ('index' or 'modal').
     *
     * @return array The sources.
     * @see sources()
     */
    protected static function defineSources(string $context = null): array
    {
        $sources = [];

        return $sources;
    }

    /**
     * inheritdoc
     * We use this to reset any field level restrictions before the values are normalised
     * E.g., if a category has a branch limit we'll reset this to store multiple values
     * @author Josh Smith <josh@batch.nz>
     * @param  string $fieldHandle string
     * @return void
     */
    protected function normalizeFieldValue(string $fieldHandle)
    {
        // Have we already normalized this value?
        if (isset($this->_normalizedFieldValues[$fieldHandle])) {
            return;
        }

        $field = $this->fieldByHandle($fieldHandle);

        if (!$field) {
            throw new Exception('Invalid field handle: ' . $fieldHandle);
        }

        // Reset any restrictions or validations on this field
        // In particular, we need to ensure there's no limits on relation fields.
        $this->resetField($field);

        $behavior = $this->getBehavior('customFields');
        $behavior->$fieldHandle = $field->normalizeValue($behavior->$fieldHandle, $this);
        $this->_normalizedFieldValues[$fieldHandle] = true;
    }

    /**
     * Resets field restrictions and settings
     * We do this so that we can store multiple values against this element
     * @author Josh Smith <josh@batch.nz>
     * @param  Field  $field
     * @return $field
     */
    protected function resetField(Field $field): Field
    {
        $field->required = false;

        if( $field instanceof BaseRelationField ){
            $field->limit = null;
        }

        if( $field instanceof CategoriesField ){
            $field->branchLimit = null;
        }

        return $field;
    }

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    protected function defineRules(): array
    {
        $rules = parent::defineRules();

        // General Rules
        $rules[] = [['id', 'productId', 'typeId'], 'integer'];
        $rules[] = [['fields'], ArrayValidator::class];
        $rules[] = [['settings'], ArrayValidator::class];

        // Ensure valid settings types are set
        $rules[] = [['settings'], function($attr, $params){
            $settingsTypes = VariantConfigurationSetting::VALID_SETTINGS_TYPES;

            foreach ($this->$attr as $key => $value) {
                if( !in_array($key, $settingsTypes) ){
                    $this->addError('settings', 'Invalid setting "'.$key.'". Expected one of ' . implode(', ', $settingsTypes) . '.');
                }
            }
        }];

        // Ensure valid settings
        $rules[] = [['settings'], function($attr, $params){
            foreach ($this->$attr as $type => $setting) {

                // Enforce model constraint
                if( !$setting instanceof VariantConfigurationSetting ){
                    return $this->addError($attr."[$type]", 'Setting must be of type ' . VariantConfigurationSetting::class . '.');
                }

                // Validate the model
                if( !$setting->validate() ){
                    $errors = [];

                    // Nest settings errors within the "settings" key
                    foreach ($setting->getErrors() as $key => $error) {
                        $errors[$attr."[$type][$key]"] = $error;
                    }

                    $this->addErrors($errors);
                }
            }
        }];

        // Default Scenario
        $rules[] = [['productId','typeId'], 'required', 'on' => self::SCENARIO_DEFAULT];

        // Field Selection & Values Scenario
        $rules[] = ['id', 'required', 'on' => [
            self::SCENARIO_SET_FIELDS,
            self::SCENARIO_SET_SETTINGS,
            self::SCENARIO_SET_VALUES
        ]];

        return $rules;
    }

    // Public Methods
    // =========================================================================

    /**
     * Defines the scenriors on this element
     * @author Josh Smith <josh@batch.nz>
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_SET_FIELDS] = ['id', 'fields'];
        $scenarios[self::SCENARIO_SET_SETTINGS] = ['id', 'settings'];
        $scenarios[self::SCENARIO_SET_VALUES] = ['id'];
        return $scenarios;
    }

    /**
     * Sets the fields that will be outputted when the element is serialized
     * @author Josh Smith <josh@batch.nz>
     * @return array
     */
    public function fields()
    {
        $baseFields = parent::fields();

        $fields = [
            'id',
            'productId',
            'typeId',
            'fields',
            'settings',
            'siteId',
            'title',
            'dateCreated',
            'dateUpdated',
            'dateDeleted',
            'status',
        ];

        // Filter out the fields we want
        $baseElementFields = array_intersect_key($baseFields, array_flip($fields));

        // Add in the variant count
        $baseElementFields['numberOfVariants'] = function(){
            return $this->getVariantsCount();
        };

        $customElementFields = [];
        $customElementFields['values'] = function(){
            $values = [];
            foreach ($this->fieldLayoutFields() as $field) {
                $value = $this->getFieldValue($field->handle);
                $values[$field->handle] = $field->serializeValue($value, $this);
            }
            return $values;
        };

        return array_merge($baseElementFields, $customElementFields);
    }

    /**
     * Returns whether the current user can edit the element.
     *
     * @return bool
     */
    public function getIsEditable(): bool
    {
        return true;
    }

    /**
     * Returns the field layout used by this element.
     *
     * @return FieldLayout|null
     */
    public function getFieldLayout()
    {
        $variantConfigurationType = $this->getVariantConfigurationType();

        if( $variantConfigurationType ){
            return $variantConfigurationType->getFieldLayout();
        }

        return null;
    }

    /**
     * Returns the variant configuration type associated with this element
     * @author Josh Smith <josh@batch.nz>
     * @return VariantConfigurationType
     */
    public function getVariantConfigurationType(): VariantConfigurationType
    {
        if ($this->typeId === null) {
            throw new InvalidConfigException('Variant Configuration Type is missing its type ID');
        }

        if (($variantConfigurationType = Plugin::getInstance()->getVariantConfigurationTypes()->getVariantConfigurationTypeById($this->typeId)) === null) {
            throw new InvalidConfigException('Invalid variant configuration type ID: '.$this->typeId);
        }

        return $variantConfigurationType;
    }

    /**
     * Returns the variant permutations from the selected field values
     * @author Josh Smith <josh@batch.nz>
     * @return array
     */
    public function getVariantPermutations(): array
    {
        // Get the transformed configuration data
        $configurationData = $this->toArray();

        // Load the custom fields
        $fields = $this
            ->getFieldLayout()
            ->getFields();

        // Create a mapping of custom field values
        $fieldHandles = array_column($fields, 'handle');
        $fieldMap = array_intersect_key($configurationData, array_flip($fieldHandles));

        return ArrayHelper::cartesian($fieldMap);
    }

    /**
     * Returns the number of variants associated with this configuration
     * @author Josh Smith <josh@batch.nz>
     * @return int
     */
    public function getVariantsCount(): int
    {
        $count = 0;
        foreach ($this->settings as $setting) {
            $count += $setting->getVariantQuery()->count();
        }
        return $count;
    }

    /**
     * Exposes the fieldByHandle method
     * @author Josh Smith <josh@batch.nz>
     * @param  string $handle
     * @return Field|null
     */
    public function getFieldByHandle(string $handle): ?Field
    {
        return $this->fieldByHandle($handle);
    }

    /**
     * Returns the associated product
     * @author Josh Smith <josh@batch.nz>
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return Commerce::getInstance()
            ->getProducts()
            ->getProductById($this->productId);
    }

    /**
     * Normalizes field values for the settings type
     * @author Josh Smith <josh@batch.nz>
     * @param  string $type        Type of settings
     * @param  array  $fieldValues An array of values keyed by field handles
     * @return mixed
     */
    public function normalizeSettingsValue($type, $fieldValues = [])
    {
        $settings = $this->settings[$type] ?? null;
        if( empty($settings) ) return null;

        switch ($settings->method) {
            case 'field':
                $elementId = $fieldValues[$settings->field] ?? null;
                $value = $settings->values[$elementId] ?? null;
                break;

            default:
                $value = null;
                break;
        }

        return $value;
    }

    /**
     * Sets settings from the request
     * @author Josh Smith <josh@batch.nz>
     * @param  string $key
     */
    public function setSettingsFromRequest($key)
    {
        $request = Craft::$app->getRequest();
        $bodyParams = $request->getBodyParam($key);

        if( empty($bodyParams) ){
            return $this;
        }

        $settings = [];
        foreach ($bodyParams as $key => $value) {
            $settings[$key] = new VariantConfigurationSetting;
            $settings[$key]->setAttributes($value);
        }
        $this->settings = $settings;

        return $this;
    }

    // Indexes, etc.
    // -------------------------------------------------------------------------

    /**
     * Returns the HTML for the element’s editor HUD.
     *
     * @return string The HTML for the editor HUD
     */
    public function getEditorHtml(): string
    {
        $html = Craft::$app->getView()->renderTemplateMacro('_includes/forms', 'textField', [
            [
                'label' => Craft::t('app', 'Title'),
                'siteId' => $this->siteId,
                'id' => 'title',
                'name' => 'title',
                'value' => $this->title,
                'errors' => $this->getErrors('title'),
                'first' => true,
                'autofocus' => true,
                'required' => true
            ]
        ]);

        $html .= parent::getEditorHtml();

        return $html;
    }

    // Events
    // -------------------------------------------------------------------------

    /**
     * Performs actions before an element is saved.
     *
     * @param bool $isNew Whether the element is brand new
     *
     * @return bool Whether the element should be saved
     */
    public function beforeSave(bool $isNew): bool
    {
        return true;
    }

    /**
     * Performs actions after an element is saved.
     *
     * @param bool $isNew Whether the element is brand new
     *
     * @return void
     */
    public function afterSave(bool $isNew)
    {
         if (!$this->propagating) {
            if (!$isNew) {
                $record = VariantConfigurationRecord::findOne($this->id);

                if (!$record) {
                    throw new Exception('Invalid variant configuration ID: ' . $this->id);
                }
            } else {
                $record = new VariantConfigurationRecord();
                $record->id = $this->id;
            }

            $record->productId = $this->productId;
            $record->typeId = $this->typeId;
            $record->fields = (empty($this->fields) ? NULL : Json::encode($this->fields));
            $record->settings = (empty($this->settings) ? NULL : Json::encode($this->settings));

            $record->save(false);

            $this->id = $record->id;
        }

        return parent::afterSave($isNew);
    }

    /**
     * Performs actions before an element is deleted.
     *
     * @return bool Whether the element should be deleted
     */
    public function beforeDelete(): bool
    {
        return true;
    }

    /**
     * Performs actions after an element is deleted.
     *
     * @return void
     */
    public function afterDelete()
    {
    }
}
