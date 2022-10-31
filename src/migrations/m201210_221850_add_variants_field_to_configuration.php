<?php

namespace batchnz\craftcommerceuntitled\migrations;

use batchnz\craftcommerceuntitled\records\VariantConfiguration;

use craft\db\Migration;

/**
 * m201210_221850_add_variants_field_to_configuration migration.
 */
class m201210_221850_add_variants_field_to_configuration extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(VariantConfiguration::tableName(), 'variants', $this->text()->after('settings'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m201210_221850_add_variants_field_to_configuration cannot be reverted.\n";
        return false;
    }
}
