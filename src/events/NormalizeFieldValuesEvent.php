<?php
/**
 * Craft Commerce Untitled plugin for Craft CMS 3.x
 *
 * Manage commerce variants by their field types and values
 *
 * @link      https://www.batch.nz
 * @copyright Copyright (c) 2020 Josh Smith
 */

namespace batchnz\craftcommerceuntitled\events;

use craft\events\CancelableEvent;

/**
 * Class NormalizeFieldValuesEvent
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 2.0
 */
class NormalizeFieldValuesEvent extends CancelableEvent
{
    /**
     * @var Field The field.
     */
    public $field;

    /**
     * @var array The normalized field values
     */
    public $fieldValues = [];
}
