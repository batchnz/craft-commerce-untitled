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

use batchnz\craftcommerceuntitled\records\VariantConfiguration as VariantConfigurationRecord;

use Craft;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;

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
     * inheritdox
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
            "$tableName.typeId",
            "$tableName.productId",
        ]);

        if ($this->typeId) {
            $this->subQuery->andWhere(Db::parseParam("$tableName.typeId", $this->typeId));
        }

        if ($this->productId) {
            $this->subQuery->andWhere(Db::parseParam("$tableName.productId", $this->productId));
        }

        return parent::beforePrepare();
    }
}
