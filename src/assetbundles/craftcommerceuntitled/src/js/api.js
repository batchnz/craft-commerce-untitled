/**
 * Fetches variant configurations from the API
 * @author Josh Smith <josh@batch.nz>
 * @return Promise
 */
const getVariantConfigurations = async (params = {}) => {
  return await fetch(getApiUrl("variant-configurations", params));
};

/**
 * Fetches variant configuration type fields from the API
 * @author Josh Smith <josh@batch.nz>
 * @return Promise
 */
const getVariantConfigurationTypeFields = async (params = {}) => {
  return await fetch(getApiUrl("variant-configuration-types/fields", params));
};

/**
 * Generates variants for the passed configuration ID
 * @author Josh Smith <josh@batch.nz>
 * @return Promise
 */
const generateVariants = async (variantConfigurationId, data = {}) => {
  return await fetch(
    getApiUrl(
      `variant-configurations/${variantConfigurationId}/variants/generate`
    ),
    {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify(data),
    }
  );
};

/**
 * Saves a variant configuration fields to the API
 * @author Josh Smith <josh@batch.nz>
 * @return Promise
 */
const saveVariantConfiguration = async (data = {}) => {
  return await fetch(getApiUrl("variant-configurations"), {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
    body: JSON.stringify(data),
  });
};

/**
 * Static method that returns an API url
 * @author Josh Smith <josh@batch.nz>
 * @param  string slug
 * @return string
 */
const getApiUrl = (slug = "", params = {}) => {
  // Build query string params
  const qs = new URLSearchParams(params).toString();

  // Assemble the api url with query string params
  // Plugin handle and API version is set in the controller
  return `${Craft.getSiteUrl() + Craft.CommerceUntitled.pluginHandle}/api/${
    Craft.CommerceUntitled.apiVersion
  }/${slug + (qs.length > 0 ? `?${qs}` : ``)}`;
};

export default {
  getVariantConfigurations,
  getVariantConfigurationTypeFields,
  saveVariantConfiguration,
  generateVariants,
};
