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

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

use craft\commerce\records\Product as CommerceProduct;

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
        // // craftcommerceuntitled_variantconfiguration table
        // $tableSchema = Craft::$app->db->schema->getTableSchema(VariantConfiguration::tableName());
        // if ($tableSchema === null) {
        //     $this->createTable(
        //         VariantConfiguration::tableName(),
        //         [
        //             'id' => $this->primaryKey(),
        //             'dateCreated' => $this->dateTime()->notNull(),
        //             'dateUpdated' => $this->dateTime()->notNull(),
        //             'uid' => $this->uid(),
        //         ]
        //     );
        // }

        // Products Table
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

    }

    /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys()
    {
        // Products Table Foreign Key
        $this->addForeignKey(
            $this->db->getForeignKeyName(Product::tableName(), 'id'),
            Product::tableName(),
            'id',
            CommerceProduct::tableName(),
            'id',
            'CASCADE',
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
    }

    /**
     * Removes the tables needed for the Records used by the plugin
     *
     * @return void
     */
    protected function removeTables()
    {
        $this->dropTableIfExists(VariantConfiguration::tableName());
        $this->dropTableIfExists(Product::tableName());
    }
}
