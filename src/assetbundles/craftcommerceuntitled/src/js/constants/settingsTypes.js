// Define the available settings types
export const TYPE_PRICE = "price";
export const TYPE_STOCK = "stock";
export const TYPE_SKU = "sku";
export const TYPE_WEIGHT = "weight";
export const TYPE_LENGTH = "length";
export const TYPE_WIDTH = "width";
export const TYPE_HEIGHT = "height";


// Define the available settings type properties.
// This looks like:
// {
//   [TYPE]: {
//     METHOD: '',
//     FIELD: '',
//     VALUES: {}
//   }
// }
export const METHOD = "method";
export const FIELD = "field";
export const VALUES = "values";

export const TYPES = [TYPE_PRICE, TYPE_STOCK, TYPE_SKU];
export const DIMENSIONS_DEPENDANT_TYPES = [TYPE_WEIGHT, TYPE_LENGTH, TYPE_WIDTH, TYPE_HEIGHT];
export const METHOD_TYPES = ["all", "skip", "field"];
