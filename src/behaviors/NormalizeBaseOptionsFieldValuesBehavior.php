<?php

namespace batchnz\craftcommerceuntitled\behaviors;

/**
 * Behavior that adds methods that normalize all values on a base options field
 */
class NormalizeBaseOptionsFieldValuesBehavior extends NormalizeBaseFieldValuesBehavior
{
    /**
     * Returns the normalized values for the field
     * @author Josh Smith <josh@batch.nz>
     * @param  ElementInterface|null $element
     * @return array
     */
    protected function getNormalizedValues(ElementInterface $element = null): array
    {
        $fieldValues = [];

        foreach ($this->owner->options as $option) {
            $fieldValues[] = [
                'label' => $option['label'],
                'value' => (string) $option['value'],
            ];
        }

        return $fieldValues;
    }
}
