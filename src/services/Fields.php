<?php
/**
 * Craft Commerce Untitled plugin for Craft CMS 3.x
 *
 * Manage commerce variants by their field types and values
 *
 * @link      https://www.batch.nz
 * @copyright Copyright (c) 2020 Josh Smith
 */

namespace batchnz\craftcommerceuntitled\services;

use craft\base\Component;
use craft\models\FieldLayout;

/**
 * Fields Service
 *
 * @author    Josh Smith
 * @package   CraftCommerceUntitled
 * @since     1.0.0
 */
class Fields extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * Returns field data on the layout with normalized values
     * @author Josh Smith <josh@batch.nz>
     * @param  FieldLayout $fieldLayout
     * @return array
     */
    public function getLayoutFieldData(FieldLayout $fieldLayout): array
    {
        $fieldData = [];
        foreach ($fieldLayout->getCustomFields() as $field) {
            $fieldData[] = [
                'name' => $field->name,
                'handle' => $field->handle,
                'instructions' => $field->instructions,
                'values' => $field->normalizeAllValues(),
            ];
        }

        return $fieldData;
    }
}
