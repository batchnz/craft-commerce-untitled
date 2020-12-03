import Vuex from "vuex";
import Vue from "vue";
import Api from "../api";

Vue.use(Vuex);

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
    SET_VARIANT_CONFIGURATIONS(state, payload) {
      state.variantConfigurations = payload;
    },
    SET_VARIANT_CONFIGURATION_TYPE_FIELDS(state, payload) {
      state.variantConfigurationTypeFields = payload;
    },
    SET_IS_LOADING(state, payload) {
      state.ui.isLoading = payload;
    },
    SET_VARIANT_CONFIGURATION(state, payload) {
      state.variantConfiguration = payload;
    },
    UPDATE_VARIANT_CONFIGURATION(state, payload) {
      state.variantConfigurations = {
        ...state.variantConfigurations,
        ...payload,
      };
    },
    SET_VARIANT_CONFIGURATION_TITLE(state, payload) {
      state.variantConfiguration.title = payload;
    },
    SET_VARIANT_CONFIGURATION_FIELDS(state, payload) {
      state.variantConfiguration.fields = payload;
    },
    SET_VARIANT_CONFIGURATION_VALUES(state, payload) {
      state.variantConfiguration.values = payload;
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
      commit("SET_VARIANT_CONFIGURATIONS", variantConfigurations);
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
        "SET_VARIANT_CONFIGURATION_TYPE_FIELDS",
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
        return commit("SET_VARIANT_CONFIGURATION_FIELDS", []);
      }

      const variantFields = [];
      state.variantConfigurationTypeFields.forEach((field) => {
        variantFields.push(field.handle);
      });

      commit("SET_VARIANT_CONFIGURATION_FIELDS", variantFields);
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

      commit("SET_VARIANT_CONFIGURATION_VALUES", variantValues);
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
    fieldValuesByHandle(state) {
      const values = {};
      state.variantConfigurationTypeFields.forEach((field) => {
        values[field.handle] = field.values.map(({ value }) => value);
      });
      return values;
    },
  },
});
