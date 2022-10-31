<?php

namespace batchnz\craftcommerceuntitled\helpers;

/**
 * Array Helper class
 * @since  1.0.0
 */
class ArrayHelper
{
    /**
     * Returns the cartesian product of the input
     * @see https://stackoverflow.com/questions/6311779/finding-cartesian-product-with-php-associative-arrays/15973172#15973172
     * @author Josh Smith <josh@batch.nz>
     * @param  array $input
     * @return array
     */
    public static function cartesian(array $input): array
    {
        $result = [[]];

        foreach ($input as $key => $values) {
            if (empty(array_filter($values))) {
                continue;
            }

            $append = [];
            foreach ($result as $product) {
                foreach ($values as $item) {
                    $product[$key] = $item;
                    $append[] = $product;
                }
            }

            $result = $append;
        }

        return $result;
    }
}
