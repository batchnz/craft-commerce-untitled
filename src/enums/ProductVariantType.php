<?php

namespace batchnz\craftcommerceuntitled\enums;

/**
 * The ProductVariantType class is an abstract class that defines all of the variant type states that are available
 * in this plugin.
 * This class is a poor man's version of an enum, since PHP does not have support for native enumerations.
 *
 * @author Josh Smith <josh@batch.nz>
 * @since 1.0.0
 */
abstract class ProductVariantType
{
    /**
     * Define ENUM values as constants
     */
    public const Standard = 'standard';
    public const Configurable = 'configurable';
}
