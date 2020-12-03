import Vue from "vue";
import Vuex from "vuex";
import VueFormulate from "@braid/vue-formulate";

import Api from "../api";
import * as MUTATIONS from "../constants/mutation-types";
import * as SETTINGS from "../constants/settings-types";

// Init Vue libs
Vue.use(Vuex);
Vue.use(VueFormulate);

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
 * Vuex Store
 * @type {Object}
 */
export default new Vuex.Store({
  state: {
    ui: {
      isLoading: false,
    },
    step: 0,
    totalSteps: 5,
    variantConfiguration: {
      id: null,
      productId: null,
      typeId: null,
      fields: [],
      values: [],
      settings: {},
    },
    variantConfigurations: [],
    variantConfigurationTypeFields: [],
  },
  mutations: {
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
    [MUTATIONS.SET_IS_LOADING](state, payload) {
      state.ui.isLoading = payload;
    },

    /**
     * Sets a variant configuration object
     * @author Josh Smith <josh@batch.nz>
     * @since  1.0.0
     * @param object state
     * @param object payload
     */
    [MUTATIONS.SET_VARIANT_CONFIGURATION](state, payload) {
      state.variantConfiguration = payload;
    },

    /**
     * Sets variant configurations
     * @author Josh Smith <josh@batch.nz>
     * @since  1.0.0
     * @param object state
     * @param object payload
     */
    [MUTATIONS.UPDATE_VARIANT_CONFIGURATION](state, payload) {
      state.variantConfigurations = {
        ...state.variantConfigurations,
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
  },
  actions: {
    /**
     * Loads required variant configurations from the API
     * @author Josh Smith <josh@batch.nz>
     * @return Promise
     */
    async fetchVariantConfigurations({ commit }, params) {
      const response = await Api.getVariantConfigurations(params);
      const { data: variantConfigurations } = await response.json();
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
  },
  getters: {
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
    selectedFieldValuesByHandle(state, getters) {
      const values = {};

      // Loop the fields and determine the available values
      getters.selectedFields.forEach((field) => {
        values[field.handle] = [];
        field.values.forEach((fieldVal) => {
          if (state.variantConfiguration.values.includes(fieldVal.value)) {
            values[field.handle].push(fieldVal);
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
     * Returns all variant configuration field type raw value objecst, keyed by field handle
     * @author Josh Smith <josh@batch.nz>
     * @param  object state
     * @return object
     */
    fieldValuesRawByHandle(state) {
      const values = {};
      state.variantConfigurationTypeFields.forEach((field) => {
        values[field.handle] = field.values.map((value) => value);
      });
      return values;
    },

    defaultSettings() {
      return defaultSettings;
    },
  },
});
