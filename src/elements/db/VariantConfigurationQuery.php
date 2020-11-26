<?php
/**
 * Craft Commerce Untitled plugin for Craft CMS 3.x
 *
 * Manage commerce variants by their field types and values
 *
 * @link      https://www.batch.nz
 * @copyright Copyright (c) 2020 Josh Smith
 */

namespace batchnz\craftcommerceuntitled\elements\db;

use batchnz\craftcommerceuntitled\models\VariantConfigurationSetting;
use batchnz\craftcommerceuntitled\records\VariantConfiguration as VariantConfigurationRecord;

use Craft;
use craft\base\ElementInterface;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;
use craft\helpers\Json;

/**
 * Query class for the variant configuration query element
 */
class VariantConfigurationQuery extends ElementQuery {

    /**
     * @var integer The configuration type ID
     */
    public $typeId;

    /**
     * @var integer The product this configuration applies to
     */
    public $productId;

    /**
     * Filters the configuration by type ID
     * @author Josh Smith <josh@batch.nz>
     * @param  integer $value
     * @return this
     */
    public function typeId($value)
    {
        $this->typeId = $value;
        return $this;
    }

    /**
     * Filters the configuration by product ID
     * @author Josh Smith <josh@batch.nz>
     * @param  integer $value
     * @return this
     */
    public function productId($value)
    {
        $this->productId = $value;
        return $this;
    }

    /**
     * inheritdoc
     * @author Josh Smith <josh@batch.nz>
     * @return boolean
     */
    protected function beforePrepare(): bool
    {
        // Grab the raw table name
        $tableName = Craft::$app
            ->getDb()
            ->getSchema()
            ->getRawTableName(VariantConfigurationRecord::tableName());

        $this->joinElementTable($tableName);

        $this->query->select([
            "$tableName.id",
            "$tableName.productId",
            "$tableName.typeId",
            "$tableName.fields",
            "$tableName.settings",
        ]);

        if ($this->typeId) {
            $this->subQuery->andWhere(Db::parseParam("$tableName.typeId", $this->typeId));
        }

        if ($this->productId) {
            $this->subQuery->andWhere(Db::parseParam("$tableName.productId", $this->productId));
        }

        return parent::beforePrepare();
    }

    /**
     * Creates a Variant Configuration Element from the query result
     * @author Josh Smith <josh@batch.nz>
     * @param  array  $row
     * @return VariantConfiguration
     */
    public function createElement(array $row): ElementInterface
    {
        $element = parent::createElement($row);

        try {
            $element->fields = Json::decodeIfJson($element->fields);
            $element->settings = Json::decodeIfJson($element->settings);

            // Populate the variant configuration settings
            if( is_array($element->settings) ){
                foreach ($element->settings as $key => $setting) {
                    $element->settings[$key] = new VariantConfigurationSetting($setting);
                }
            }

        } catch(\Exception $e){
            // Swallow it whole
        }

        return $element;
    }
}
