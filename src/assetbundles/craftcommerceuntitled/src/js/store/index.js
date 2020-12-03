import Vuex from "vuex";
import Vue from "vue";
import Api from "../api";

import * as MUTATIONS from "../constants/mutation-types";
import * as SETTINGS from "../constants/settings-types";

Vue.use(Vuex);

const defaultSettings = {
  [SETTINGS.METHOD]: null,
  [SETTINGS.FIELD]: null,
  [SETTINGS.VALUES]: {},
};

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
    [MUTATIONS.SET_VARIANT_CONFIGURATIONS](state, payload) {
      state.variantConfigurations = payload;
    },
    [MUTATIONS.SET_VARIANT_CONFIGURATION_TYPE_FIELDS](state, payload) {
      state.variantConfigurationTypeFields = payload;
    },
    [MUTATIONS.SET_IS_LOADING](state, payload) {
      state.ui.isLoading = payload;
    },
    [MUTATIONS.SET_VARIANT_CONFIGURATION](state, payload) {
      state.variantConfiguration = payload;
    },
    [MUTATIONS.UPDATE_VARIANT_CONFIGURATION](state, payload) {
      state.variantConfigurations = {
        ...state.variantConfigurations,
        ...payload,
      };
    },
    [MUTATIONS.SET_VARIANT_CONFIGURATION_TITLE](state, payload) {
      state.variantConfiguration.title = payload;
    },
    [MUTATIONS.SET_VARIANT_CONFIGURATION_FIELDS](state, payload) {
      state.variantConfiguration.fields = payload;
    },
    [MUTATIONS.SET_VARIANT_CONFIGURATION_VALUES](state, payload) {
      state.variantConfiguration.values = payload;
    },
    [MUTATIONS.SET_VARIANT_CONFIGURATION_SETTINGS](state, payload) {
      state.variantConfiguration.settings = payload;
    },
    [MUTATIONS.SET_VARIANT_CONFIGURATION_SETTINGS_TYPE](state, { type, data }) {
      if (!state.variantConfiguration[type]) {
        state.variantConfiguration[type] = {};
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
    isAllFieldsSelected(state) {
      const handles = state.variantConfigurationTypeFields.map(
        (field) => field.handle
      );
      const filteredArray = handles.filter((value) =>
        state.variantConfiguration.fields.includes(value)
      );

      return filteredArray.length === handles.length;
    },
    isAllValuesSelected(state, getters) {
      return (handle) => {
        const values = getters.fieldValuesByHandle[handle];

        const filteredArray = values.filter((value) =>
          state.variantConfiguration.values.includes(value)
        );

        return filteredArray.length === values.length;
      };
    },
    selectedFields(state) {
      return state.variantConfigurationTypeFields.filter((field) =>
        state.variantConfiguration.fields.includes(field.handle)
      );
    },
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
    fieldValuesByHandle(state) {
      const values = {};
      state.variantConfigurationTypeFields.forEach((field) => {
        values[field.handle] = field.values.map(({ value }) => value);
      });
      return values;
    },
    defaultSettings() {
      return defaultSettings;
    },
  },
});
