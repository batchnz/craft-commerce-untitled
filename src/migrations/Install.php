<?php
/**
 * Craft Commerce Untitled plugin for Craft CMS 3.x
 *
 * Manage commerce variants by their field types and values
 *
 * @link      https://www.batch.nz
 * @copyright Copyright (c) 2020 Josh Smith
 */

namespace batchnz\craftcommerceuntitled\migrations;

use batchnz\craftcommerceuntitled\Plugin;
use batchnz\craftcommerceuntitled\enums\ProductVariantType;
use batchnz\craftcommerceuntitled\records\Product;
use batchnz\craftcommerceuntitled\records\VariantConfiguration;
use batchnz\craftcommerceuntitled\records\VariantConfigurationType;

use Craft;
use craft\db\Migration;
use craft\db\Table;

use craft\commerce\Plugin as CommercePlugin;
use craft\commerce\db\Table as CommerceTable;

/**
 * Craft Commerce Untitled Install Migration
 *
 * If your plugin needs to create any custom database tables when it gets installed,
 * create a migrations/ folder within your plugin folder, and save an Install.php file
 * within it using the following template:
 *
 * If you need to perform any additional actions on install/uninstall, override the
 * safeUp() and safeDown() methods.
 *
 * @author    Josh Smith
 * @package   CraftCommerceUntitled
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * This method contains the logic to be executed when applying this migration.
     * This method differs from [[up()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[up()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;

        $this->createTables();
        $this->createIndexes();
        $this->addForeignKeys();

        // Refresh the db schema caches
        Craft::$app->db->schema->refresh();
        $this->insertDefaultData();

        return true;
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * This method differs from [[down()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[down()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables needed for the Records used by the plugin
     *
     * @return bool
     */
    protected function createTables()
    {
        // craftcommerceuntitled_variantconfigurations table
        $tableSchema = Craft::$app->db->schema->getTableSchema(VariantConfiguration::tableName());
        if ($tableSchema === null) {
            $this->createTable(
                VariantConfiguration::tableName(),
                [
                    'id' => $this->primaryKey(),
                    'productId' => $this->integer()->notNull(),
                    'typeId' => $this->integer()->notNull(),
                    'fields' => $this->text(),
                    'settings' => $this->text(),
                    'variants' => $this->text(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                ]
            );
        }

        // craftcommerceuntitled_variantconfiguration_types table
        $tableSchema = Craft::$app->db->schema->getTableSchema(VariantConfigurationType::tableName());
        if ($tableSchema === null) {
            $this->createTable(
                VariantConfigurationType::tableName(),
                [
                    'id' => $this->primaryKey(),
                    'productTypeId' => $this->integer()->notNull(),
                    'fieldLayoutId' => $this->integer(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                ]
            );
        }

        // craftcommerceuntitled_products table
        $tableSchema = Craft::$app->db->schema->getTableSchema(Product::tableName());
        if ($tableSchema === null) {
            $this->createTable(
                Product::tableName(),
                [
                    'id' => $this->primaryKey(),
                    'variantType' => $this->enum('variantType', [
                        ProductVariantType::Standard,
                        ProductVariantType::Configurable
                    ])->defaultValue(ProductVariantType::Standard)->notNull(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                ]
            );
        }
    }

    /**
     * Creates the indexes needed for the Records used by the plugin
     *
     * @return void
     */
    protected function createIndexes()
    {
        $this->createIndex(null, Product::tableName(), ['variantType'], false);
        $this->createIndex(null, VariantConfiguration::tableName(), ['typeId'], false);
        $this->createIndex(null, VariantConfiguration::tableName(), ['productId'], false);
        $this->createIndex(null, VariantConfigurationType::tableName(), ['productTypeId'], false);
        $this->createIndex(null, VariantConfigurationType::tableName(), ['fieldLayoutId'], false);
    }

    /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys()
    {
        // Variant Configuration to Elements
        $this->addForeignKey(
            $this->db->getForeignKeyName(VariantConfiguration::tableName(), 'id'),
            VariantConfiguration::tableName(),
            'id',
            Table::ELEMENTS,
            'id',
            'CASCADE'
        );

        // Variant Configuration to Products
        $this->addForeignKey(
            $this->db->getForeignKeyName(VariantConfiguration::tableName(), 'productId'),
            VariantConfiguration::tableName(),
            'productId',
            CommerceTable::PRODUCTS,
            'id',
            'CASCADE'
        );

        // Variant Configuration to Variant Configuration Type
        $this->addForeignKey(
            $this->db->getForeignKeyName(VariantConfiguration::tableName(), 'typeId'),
            VariantConfiguration::tableName(),
            'typeId',
            VariantConfigurationType::tableName(),
            'id',
            'CASCADE'
        );

        // Variant Configuration Type to Products
        $this->addForeignKey(
            $this->db->getForeignKeyName(VariantConfigurationType::tableName(), 'productTypeId'),
            VariantConfigurationType::tableName(),
            'productTypeId',
            CommerceTable::PRODUCTTYPES,
            'id',
            'CASCADE'
        );

        // Variant Configuration Type to Field Layouts
        $this->addForeignKey(
            $this->db->getForeignKeyName(VariantConfigurationType::tableName(), 'fieldLayoutId'),
            VariantConfigurationType::tableName(),
            'fieldLayoutId',
            Table::FIELDLAYOUTS,
            'id',
            'SET NULL'
        );

        // Products Table Foreign Key
        $this->addForeignKey(
            $this->db->getForeignKeyName(Product::tableName(), 'id'),
            Product::tableName(),
            'id',
            CommerceTable::PRODUCTS,
            'id',
            'CASCADE'
        );
    }

    /**
     * Populates the DB with the default data.
     *
     * @return void
     */
    protected function insertDefaultData()
    {
        // Fetch all product types
        $productTypes = CommercePlugin::getInstance()
            ->getProductTypes()
            ->getAllProductTypes();

        if (empty($productTypes)) {
            return;
        }

        foreach ($productTypes as $productType) {
            // Create a new variant configuration type
            $variantConfigurationType = new VariantConfigurationType([
                'productTypeId' => $productType->id
            ]);

            // Create the variant configuration type
            Plugin::getInstance()
                ->getVariantConfigurationTypes()
                ->saveVariantConfigurationType($variantConfigurationType);
        }

        return;
    }

    /**
     * Removes the tables needed for the Records used by the plugin
     *
     * @return void
     */
    protected function removeTables()
    {
        $this->dropTableIfExists(VariantConfiguration::tableName());
        $this->dropTableIfExists(VariantConfigurationType::tableName());
        $this->dropTableIfExists(Product::tableName());
    }
}
