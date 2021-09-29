import Vuex from "vuex";
import Vue from "vue";

import Api from "../api";
import State from "./stepState";
import AsyncEventBus from "./asyncEventBus";

import * as MUTATIONS from "../constants/mutationTypes";
import * as SETTINGS from "../constants/settingsTypes";
import {DIMENSIONS_DEPENDANT_TYPES, TYPES} from "../constants/settingsTypes";

Vue.use(Vuex);

/**
 * Defines the default variant configuration settings
 * Used with mutations to ensure all properties exist in settings objects
 * @type {Object}
 */
const defaultSettings = {
  [SETTINGS.METHOD]: null,
  [SETTINGS.FIELD]: null,
  [SETTINGS.VALUES]: {},
};

/**
 * Returns the default settings object for the store
 * @author Josh Smith <josh@batch.nz>
 * @return object
 */
const getSettingsDefaults = () => {
  const settings = {};
  SETTINGS.TYPES.forEach((type) => {
    settings[type] = { ...defaultSettings };
  });
  return settings;
};

/**
 * Retunrs a new variant configuration object
 * @author Josh Smith <josh@batch.nz>
 * @return object
 */
const getNewVariantConfiguration = () => {
  return {
    id: null,
    title: "",
    fields: [],
    values: [],
    settings: getSettingsDefaults(),
  };
};

/**
 * Transforms variant configurations API
 * response into a format suitable for the store.
 *
 * Mutates the variant configurations by reference
 *
 * @author Josh Smith <josh@batch.nz>
 * @param  {Array} response An array of response data
 * @return void
 */
const transformResponse = (response = []) => {
  if (!Array.isArray(response)) {
    response = [response];
  }

  response.forEach((vc) => {
    // Flatten the values from a key-pair object to an array
    let values = [];
    for (const [key, value] of Object.entries(vc.values)) {
      values = values.concat(vc.values[key]);
    }
    vc.values = values;
  });
};

/**
 * Vuex Store
 * @type {Object}
 */
export default new Vuex.Store({
  state: {
    ui: {
      isLoading: false,
      isSubmitting: false,
      isCompleted: false,
    },
    step: 0,
    totalSteps: 5,
    formErrors: {},
    productId: null,
    productTypeId: null,
    typeId: null,
    variantConfiguration: getNewVariantConfiguration(),
    variantConfigurations: [],
    variantConfigurationTypeFields: [],
    hasDimensions: null,
  },
  mutations: {
    /**
     * Sets the product ID
     * @author Josh Smith <josh@batch.nz>
     * @since  1.0.0
     * @param object state
     * @param object payload
     */
    [MUTATIONS.SET_PRODUCT_ID](state, payload) {
      state.productId = payload;
    },

    /**
     * Sets the product type ID
     * @author Josh Smith <josh@batch.nz>
     * @since  1.0.0
     * @param object state
     * @param object payload
     */
    [MUTATIONS.SET_PRODUCT_TYPE_ID](state, payload) {
      state.productTypeId = payload;
    },

    /**
     * Sets the variant configuration type ID
     * @author Josh Smith <josh@batch.nz>
     * @since  1.0.0
     * @param object state
     * @param object payload
     */
    [MUTATIONS.SET_TYPE_ID](state, payload) {
      state.typeId = payload;
    },

    /**
     * Sets the variant configurations data
     * @author Josh Smith <josh@batch.nz>
     * @since  1.0.0
     * @param object state
     * @param object payload
     */
    [MUTATIONS.SET_VARIANT_CONFIGURATIONS](state, payload) {
      state.variantConfigurations = payload;
    },

    /**
     * Sets whether the allow dimensions option has been ticked for
     * the current product
     * @author Daniel Siemers <daniel@batch.nz>
     * @since  1.0.0
     * @param object state
     * @param object payload
     */
    [MUTATIONS.SET_HAS_DIMENSIONS](state, payload) {
      state.hasDimensions = payload;
    },

    /**
     * Sets a variant configuration object
     * @author Josh Smith <josh@batch.nz>
     * @since  1.0.0
     * @param object state
     * @param object payload
     */
    [MUTATIONS.UPDATE_VARIANT_CONFIGURATIONS](state, payload) {
      state.variantConfigurations.forEach((vc, i) => {
        if (vc.id === payload.id) {
          state.variantConfigurations[i] = { ...vc, ...payload };
        }
      });
    },

    /**
     * sets the variant configuration type fields data
     * @author Josh Smith <josh@batch.nz>
     * @since  1.0.0
     * @param object state
     * @param object payload
     */
    [MUTATIONS.SET_VARIANT_CONFIGURATION_TYPE_FIELDS](state, payload) {
      state.variantConfigurationTypeFields = payload;
    },

    /**
     * Sets the UI loading state
     * @author Josh Smith <josh@batch.nz>
     * @since  1.0.0
     * @param object state
     * @param object payload
     */
    [MUTATIONS.SET_IS_LOADING](state, payload = true) {
      state.ui.isLoading = payload;
    },

    /**
     * Sets the UI submitting state
     * @author Josh Smith <josh@batch.nz>
     * @since  1.0.0
     * @param object state
     * @param object payload
     */
    [MUTATIONS.SET_IS_SUBMITTING](state, payload = true) {
      state.ui.isSubmitting = payload;
    },

    /**
     * Sets the UI completed state
     * @author Josh Smith <josh@batch.nz>
     * @since  1.0.0
     * @param object state
     * @param object payload
     */
    [MUTATIONS.SET_IS_COMPLETED](state, payload = true) {
      state.ui.isCompleted = payload;
    },

    /**
     * Sets a variant configuration object
     * @author Josh Smith <josh@batch.nz>
     * @since  1.0.0
     * @param object state
     * @param object payload
     */
    [MUTATIONS.SET_VARIANT_CONFIGURATION](state, payload) {
      state.variantConfiguration = { ...payload };
    },

    /**
     * Updates a variant configuration
     * @author Josh Smith <josh@batch.nz>
     * @since  1.0.0
     * @param object state
     * @param object payload
     */
    [MUTATIONS.UPDATE_VARIANT_CONFIGURATION](state, payload) {
      state.variantConfiguration = {
        ...state.variantConfiguration,
        ...payload,
      };
    },

    /**
     * Sets the variant configuration title
     * @author Josh Smith <josh@batch.nz>
     * @since  1.0.0
     * @param object state
     * @param object payload
     */
    [MUTATIONS.SET_VARIANT_CONFIGURATION_TITLE](state, payload) {
      state.variantConfiguration.title = payload;
    },

    /**
     * Sets variant configuration fields
     * @author Josh Smith <josh@batch.nz>
     * @since  1.0.0
     * @param object state
     * @param object payload
     */
    [MUTATIONS.SET_VARIANT_CONFIGURATION_FIELDS](state, payload) {
      state.variantConfiguration.fields = payload;
    },

    /**
     * Sets configuration values
     * @author Josh Smith <josh@batch.nz>
     * @since  1.0.0
     * @param object state
     * @param object payload
     */
    [MUTATIONS.SET_VARIANT_CONFIGURATION_VALUES](state, payload) {
      state.variantConfiguration.values = payload;
    },

    /**
     * Sets the entire variant settings object
     * @author Josh Smith <josh@batch.nz>
     * @since  1.0.0
     * @param object state
     * @param object payload
     */
    [MUTATIONS.SET_VARIANT_CONFIGURATION_SETTINGS](state, payload) {
      state.variantConfiguration.settings = payload;
    },

    /**
     * Sets variant configuration settings by type
     * Current types are `price` and `stock`
     * Settings data is merged with defaults to ensure all properties are set
     *
     * Current defaults are:
     *   - `method`: either `all`, `field` or `skip`
     *   - `field`: The field name when method is set to `field`
     *   - `values`: An object of values for the selected field. A key of `value` is used for non relation values
     *
     * @author Josh Smith <josh@batch.nz>
     * @since  1.0.0
     * @param object state
     * @param string type The settings type (`price` or `stock`)
     * @param object data The data to store
     */
    [MUTATIONS.SET_VARIANT_CONFIGURATION_SETTINGS_TYPE](state, { type, data }) {
      if (!state.variantConfiguration.settings[type]) {
        state.variantConfiguration.settings[type] = {};
      }

      state.variantConfiguration.settings = {
        ...state.variantConfiguration.settings,
        ...{
          [type]: {
            ...state.variantConfiguration.settings[type],
            ...{ ...defaultSettings, ...data },
          },
        },
      };
    },

    /**
     * Sets the current step
     */
    [MUTATIONS.SET_STEP](state, step) {
      state.step = step;
    },

    /**
     * Sets the form errors
     */
    [MUTATIONS.SET_FORM_ERRORS](state, errors) {
      state.formErrors = errors;
    },
  },
  actions: {
    /**
     * Resets the VC form
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    async resetForm({ commit, dispatch }) {
      dispatch("createNewVariantConfiguration");
      commit(MUTATIONS.SET_IS_COMPLETED, false);
      commit(MUTATIONS.SET_STEP, 0);
    },

    /**
     * Saves a VC to the server
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    async saveVariantConfiguration({ commit, state, getters, dispatch }) {
      commit(MUTATIONS.SET_IS_SUBMITTING, true);

      // Transform the fields/values for the API request
      const values = {};
      const fields = Object.keys(getters.fieldValuesByHandle);

      fields.forEach((fieldHandle) => {
        values[fieldHandle] =
          getters.selectedOptionValuesByFieldHandle[fieldHandle] || [];
      });

      const response = await Api.saveVariantConfiguration({
        ...state.variantConfiguration,
        ...{
          values,
          productId: state.productId,
          typeId: state.typeId,
        },
      });
      const { data: variantConfiguration } = await response.json();

      // Transform the response into a format suitable for the store
      transformResponse(variantConfiguration);

      // Save and replace the saved variant configuration into the store
      commit(MUTATIONS.SET_VARIANT_CONFIGURATION, variantConfiguration);
      commit(MUTATIONS.UPDATE_VARIANT_CONFIGURATIONS, variantConfiguration);

      commit(MUTATIONS.SET_IS_SUBMITTING, false);
    },

    /**
     * Generates variants for the passed config ID
     * @author Josh Smith <josh@batch.nz>
     * @param  integer variantConfigurationId
     * @return void
     */
    async generateVariants({ commit }, variantConfigurationId) {
      commit(MUTATIONS.SET_IS_SUBMITTING, true);
      const response = await Api.generateVariants(variantConfigurationId);
      commit(MUTATIONS.SET_IS_SUBMITTING, false);
    },

    /**
     * Sets the current editable variant configuration
     * by fetching the vc from loaded data by ID
     * @author Josh Smith <josh@batch.nz>
     * @param  int variantConfigurationId
     */
    async setCurrentVariantConfigurationById(
      { commit, state },
      variantConfigurationId
    ) {
      const variantConfiguration = state.variantConfigurations.find(
        (vc) => vc.id === variantConfigurationId
      );

      if (variantConfiguration == null) {
        throw new Error("Variant Configuration not found");
      }

      commit(MUTATIONS.SET_VARIANT_CONFIGURATION, variantConfiguration);
      commit(MUTATIONS.SET_STEP, 1);
    },

    /**
     * Loads required variant configurations from the API
     * @author Josh Smith <josh@batch.nz>
     * @return Promise
     */
    async fetchVariantConfigurations({ commit }, params) {
      const response = await Api.getVariantConfigurations(params);
      const { data: variantConfigurations } = await response.json();

      // Transform the response into a format suitable for the store
      transformResponse(variantConfigurations);

      commit(MUTATIONS.SET_VARIANT_CONFIGURATIONS, variantConfigurations);
    },

    /**
     * Loads required variant configurations type fields from the API
     * @author Josh Smith <josh@batch.nz>
     * @return Promise
     */
    async fetchVariantConfigurationTypeFields({ commit }, params) {
      const response = await Api.getVariantConfigurationTypeFields(params);
      const { data: variantConfigurationTypeFields } = await response.json();
      commit(
        MUTATIONS.SET_VARIANT_CONFIGURATION_TYPE_FIELDS,
        variantConfigurationTypeFields
      );
    },

    /**
     * Toggles all selected variant configuration fields
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    async toggleAllSelectedFields({ commit, getters, state }) {
      if (getters.isAllFieldsSelected) {
        return commit(MUTATIONS.SET_VARIANT_CONFIGURATION_FIELDS, []);
      }

      const variantFields = [];
      state.variantConfigurationTypeFields.forEach((field) => {
        variantFields.push(field.handle);
      });

      commit(MUTATIONS.SET_VARIANT_CONFIGURATION_FIELDS, variantFields);
    },

    /**
     * Toggles all selected values for the passed handle
     * @author Josh Smith <josh@batch.nz>
     * @param  string   handle
     * @return Promise
     */
    async toggleAllSelectedValues({ commit, getters, state }, handle) {
      const values = getters.fieldValuesByHandle[handle];
      if (!values.length) return;

      let variantValues = [];
      const selectedValues = state.variantConfiguration.values;
      if (!getters.isAllValuesSelected(handle)) {
        variantValues = [...selectedValues, ...values];
      } else {
        variantValues = selectedValues.filter(
          (value) => values.indexOf(value) === -1
        );
      }

      commit(MUTATIONS.SET_VARIANT_CONFIGURATION_VALUES, variantValues);
    },

    /**
     * Navigate to the next step
     * @author Josh Smith <josh@batch.nz>
     * @param  int  step
     * @return void
     */
    async nextStep({ commit, getters, state, dispatch }) {
      const { rules } = getters.currentStepState;

      const valid = await dispatch("validate", {
        schema: rules,
        values: state.variantConfiguration,
      });
      if (!valid) return;

      // Emit an event so that components can hook into the submission
      const result = await AsyncEventBus.emit("form-submission");
      if (result === false) return;

      if (state.step >= state.totalSteps) {
        return;
      }
      commit(MUTATIONS.SET_STEP, state.step + 1);
    },

    /**
     * Navigate to the next step
     * @author Josh Smith <josh@batch.nz>
     * @param  int  step
     * @return void
     */
    async prevStep({ commit, state }) {
      if (state.step < 0) {
        return;
      }
      commit(MUTATIONS.SET_STEP, state.step - 1);
    },

    /**
     * Navigate to the menu step
     * @author Josh Smith <josh@batch.nz>
     * @param  int  step
     * @return void
     */
    async menuStep({ commit, dispatch }) {
      dispatch("resetForm");
    },

    /**
     * Validates the current form step
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    async validate({ commit, getters, state }, { schema, values }) {
      try {
        commit(MUTATIONS.SET_FORM_ERRORS, {});
        await schema.validate(values, { abortEarly: false });
      } catch (err) {
        if (err.inner) {
          err.inner.forEach((error) => {
            commit(MUTATIONS.SET_FORM_ERRORS, {
              ...state.formErrors,
              ...{ [error.path]: error.message },
            });
          });
        }

        return false;
      }

      return true;
    },

    /**
     * Creates a new variant configuration
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    async createNewVariantConfiguration({ commit, dispatch }) {
      await dispatch("clearFormErrors");
      commit(MUTATIONS.SET_VARIANT_CONFIGURATION, getNewVariantConfiguration());
    },

    /**
     * Clears the form errors
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    async clearFormErrors({ commit }) {
      commit(MUTATIONS.SET_FORM_ERRORS, {});
    },
  },
  getters: {
    /**
     * Returns the allowed settings types for the current product
     * @author Daniel Siemers <daniel@batch.nz>
     * @param object state
     * @return array
     */
    allowedTypes(state) {
      return TYPES.concat(state.hasDimensions ? DIMENSIONS_DEPENDANT_TYPES : []);
    },

    /**
     * Returns settings by type
     * @author Josh Smith <josh@batch.nz>
     * @param  object state
     * @return string
     */
    settingsByType(state) {
      return (type) => {
        return state.variantConfiguration.settings[type] || {};
      };
    },

    /**
     * Returns whether all variant configuration fields have been selected
     * @author Josh Smith <josh@batch.nz>
     * @param  object  state
     * @return Boolean
     */
    isAllFieldsSelected(state) {
      const handles = state.variantConfigurationTypeFields.map(
        (field) => field.handle
      );
      const filteredArray = handles.filter((value) =>
        state.variantConfiguration.fields.includes(value)
      );

      return filteredArray.length === handles.length;
    },

    /**
     * Returns whether all variant configuration values for the passed field handle have been selected
     * @author Josh Smith <josh@batch.nz>
     * @param  object  state
     * @param  object  getters
     * @return Boolean
     */
    isAllValuesSelected(state, getters) {
      return (handle) => {
        const values = getters.fieldValuesByHandle[handle];

        const filteredArray = values.filter((value) =>
          state.variantConfiguration.values.includes(value)
        );

        return filteredArray.length === values.length;
      };
    },

    /**
     * Returns the variant configuration field types that have been selected
     * @author Josh Smith <josh@batch.nz>
     * @param  object state
     * @return array
     */
    selectedFields(state) {
      return state.variantConfigurationTypeFields.filter((field) =>
        state.variantConfiguration.fields.includes(field.handle)
      );
    },

    /**
     * Returns the selected variant configuration field values, keyed by field handle
     * @author Josh Smith <josh@batch.nz>
     * @param  object state
     * @param  object getters
     * @return object
     */
    selectedOptionsByFieldHandle(state, getters) {
      const values = {};

      // Loop the fields and determine the available values
      getters.selectedFields.forEach((field) => {
        values[field.handle] = [];
        field.values.forEach((option) => {
          if (state.variantConfiguration.values.includes(option.value)) {
            values[field.handle].push(option);
          }
        });
      });

      return values;
    },

    /**
     * Returns all variant configuration field type values, keyed by field handle
     * @author Josh Smith <josh@batch.nz>
     * @param  object state
     * @return object
     */
    fieldValuesByHandle(state) {
      const values = {};
      state.variantConfigurationTypeFields.forEach((field) => {
        values[field.handle] = field.values.map(({ value }) => value);
      });
      return values;
    },

    /**
     * Returns all field value option labels keyed by ID
     * @author Josh Smith <josh@batch.nz>
     * @param  object state
     * @return object
     */
    optionValuesById(state) {
      const values = {};
      state.variantConfigurationTypeFields.forEach((field) => {
        field.values.forEach((option) => {
          values[option.value] = option.label;
        });
      });
      return values;
    },

    /**
     * Returns selected option values keyed by field handle
     * Used in the API request when saving configurations
     * @author Josh Smith <josh@batch.nz>
     * @param  object state
     * @return object
     */
    selectedOptionValuesByFieldHandle(state, getters) {
      const values = {};

      // Loop the fields and determine the available values
      getters.selectedFields.forEach((field) => {
        values[field.handle] = [];
        field.values.forEach((option) => {
          if (state.variantConfiguration.values.includes(option.value)) {
            values[field.handle].push(option.value);
          }
        });
      });

      return values;
    },

    /**
     * Returns the current step's state
     * @author Josh Smith <josh@batch.nz>
     * @param  object state
     * @param  object getters
     * @return object
     */
    currentStepState(state, getters) {
      return getters.stepState(state.step);
    },

    /**
     * Returns the state for each form step
     * @author Josh Smith <josh@batch.nz>
     * @return object
     */
    stepState(state, getters) {
      return (step) => {
        switch (step) {
          case 0:
          default:
            return State.indexStep;
          case 1:
            return State.nameStep;
          case 2:
            return State.fieldsStep;
          case 3:
            return State.valuesStep;
          case 4:
            return State.settingsStep;
          case 5:
            return State.generateStep;
        }
      };
    },

    /**
     * Returns the default settings object properties
     * @author Josh Smith <josh@batch.nz>
     * @return objects
     */
    defaultSettings() {
      return defaultSettings;
    },
  },
});
