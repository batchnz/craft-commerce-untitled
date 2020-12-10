// Define the available settings types
export const TYPE_PRICE = "price";
export const TYPE_STOCK = "stock";
export const TYPE_SKU = "sku";

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
export const METHOD_TYPES = ["all", "skip", "field"];
