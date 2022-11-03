<?php

namespace batchnz\craftcommerceuntitled\behaviors;

use Craft;
use craft\helpers\ElementHelper;

/**
 * Behavior that adds methods that normalize all values on a base relation field
 */
class NormalizeBaseRelationFieldValuesBehavior extends NormalizeBaseFieldValuesBehavior
{
    /**
     * Returns the normalized values for the field
     * @author Josh Smith <josh@batch.nz>
     * @param  ElementInterface|null $element
     * @return array
     */
    protected function getNormalizedValues(ElementInterface $element = null): array
    {
        // Hack alert!
        // There's currently no other way to fetch the element type which we need to configure the element query
        $method = new \ReflectionMethod($this->owner, 'elementType');
        $method->setAccessible('elementType');
        $elementType = $method->invoke(null);

        // Create relation fields element query
        $elementQuery = $elementType::find();

        // Filter down the allowed set of element sources
        if (!$this->owner->allowMultipleSources && $this->owner->source) {
            $source = ElementHelper::findSource($elementType, $this->owner->source);

            // Does the source specify any criteria attributes?
            if (isset($source['criteria'])) {
                Craft::configure($elementQuery, $source['criteria']);
            }
        }

        // Fetch elements with enough data to represent in the UI
        $elements = [];
        foreach ($elementQuery->all() as $el) {
            $elements[] = [
                'label' => $el->title,
                'value' => (string) $el->id,
            ];
        }

        return $elements;
    }
}
