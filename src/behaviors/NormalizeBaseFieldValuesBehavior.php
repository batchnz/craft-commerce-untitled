<?php

namespace batchnz\craftcommerceuntitled\behaviors;

use batchnz\craftcommerceuntitled\events\NormalizeFieldValuesEvent;
use yii\base\Behavior;

/**
 * Behavior that adds methods that normalize all values on a field
 */
class NormalizeBaseFieldValuesBehavior extends Behavior
{
    /**
     * Event that's called before field values are normalized
     */
    public const EVENT_BEFORE_NORMALIZE_FIELD_VALUES = 'beforeNormalizeFieldValues';

    /**
     * Event that's called after field values are normalized
     */
    public const EVENT_AFTER_NORMALIZE_FIELD_VALUES = 'afterNormalizeFieldValues';

    /** @var Field */
    public $owner;

    /**
     * Normalizes all the field’s values for use.
     *
     * This works the same way as the standard `normalizeValue()` method except it normalizes ALL values on the field
     *
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     * @return mixed The prepared field values
     */
    public function normalizeAllValues(ElementInterface $element = null)
    {
        $fieldValues = [];

        if ($this->owner->hasEventHandlers(self::EVENT_BEFORE_NORMALIZE_FIELD_VALUES)) {
            $normalizeFieldValuesEvent = new NormalizeFieldValuesEvent([
                'field' => $this,
                'fieldValues' => $fieldValues
            ]);
            $this->owner->trigger(self::EVENT_BEFORE_NORMALIZE_FIELD_VALUES, $normalizeFieldValuesEvent);

            if (!$normalizeFieldValuesEvent->isValid) {
                return;
            }
        }

        $fieldValues = $this->getNormalizedValues($element);

        if ($this->owner->hasEventHandlers(self::EVENT_AFTER_NORMALIZE_FIELD_VALUES)) {
            $normalizeFieldValuesEvent = new NormalizeFieldValuesEvent([
                'field' => $this,
                'fieldValues' => $fieldValues
            ]);
            $this->owner->trigger(self::EVENT_AFTER_NORMALIZE_FIELD_VALUES, $normalizeFieldValuesEvent);

            if (!$normalizeFieldValuesEvent->isValid) {
                return;
            }
        }

        return $fieldValues;
    }

    /**
     * Returns the normalized values for the field
     * @author Josh Smith <josh@batch.nz>
     * @param  ElementInterface|null $element
     * @return array
     */
    protected function getNormalizedValues(ElementInterface $element = null): array
    {
        return [];
    }
}
