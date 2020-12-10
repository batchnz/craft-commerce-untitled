import store from "./index";
import {
  TYPES,
  METHOD_TYPES,
  METHOD,
  FIELD,
  VALUES,
} from "../constants/settingsTypes";
import { object, string, required, array, lazy, number, ref } from "yup";

const defaults = {
  header: {
    title: "Variant Configurator",
    subtitle: "Select or create a variant configuration",
  },
  footer: {
    priBtnText: "Next Step",
    secBtnText: "Previous",
  },
  rules: object({}),
};

export const indexStep = {
  component: "IndexStep",
  header: defaults.header,
  footer: {
    priBtnText: "Create New",
    secBtnText: "Cancel",
  },
  rules: defaults.rules,
};

export const nameStep = {
  component: "NameStep",
  header: {
    ...defaults.header,
    subtitle: "Step 1. Set the configuration name",
  },
  footer: defaults.footer,
  rules: object().shape({
    title: string().required("Please enter a configuration name"),
  }),
};

export const fieldsStep = {
  component: "FieldsStep",
  header: {
    ...defaults.header,
    subtitle: "Step 2. Select the fields to generate variants from",
  },
  footer: defaults.footer,
  rules: object().shape({
    fields: array().of(string()).min(1, "Please select at least one field"),
  }),
};

export const valuesStep = {
  component: "ValuesStep",
  header: {
    ...defaults.header,
    subtitle: "Step 3. Select the values to generate variants from",
  },
  footer: defaults.footer,
  rules: object().shape({
    values: array().of(string()).min(1, "Please select at least one value"),
  }),
};

/**
 * Returns dynamically generated settings type rules (e.g. rules for price, stock or sku)
 * We use lazy validation to ensure the store has loaded (circular dependency) before validation rules are generated
 *
 * @author Josh Smith <josh@batch.nz>
 * @param  array    settingsTypes
 * @return object
 */
export const getSettingsTypeRules = (settingsTypes = TYPES) => {
  const rules = {};
  settingsTypes.forEach((type) => {
    rules[type] = object({
      [METHOD]: string()
        .nullable()
        .required()
        .oneOf(METHOD_TYPES, "Please select an option"),
      [FIELD]: string()
        .when("method", {
          is: "field",
          then: string().nullable().required("Please select a field"),
        })
        .nullable(),
      [VALUES]: object()
        .when("method", {
          is: "field",
          then: lazy(() => getSettingsMethodFieldRules(type)),
        })
        .when(METHOD, {
          is: "all",
          then: lazy(() => getSettingsMethodAllRules(type)),
        })
        .nullable(),
    });
  });
  return object(rules);
};

/**
 * Returns dynamically generated validation rules for each settings rule for the field method
 * Note: this is tightly coupled with the store to generate rules for the selected field handle and values
 *
 * @author Josh Smith <josh@batch.nz>
 * @param  string type
 * @return object
 */
const getSettingsMethodFieldRules = (type) => {
  const rules = {};

  const settings = store.getters.settingsByType(type);
  if (settings.field == null) return object(rules);

  const optionValues =
    store.getters.selectedOptionValuesByFieldHandle[settings.field];
  if (optionValues == null) return object(rules);

  // Loop each field Id and generate schema rules
  optionValues.forEach((value) => {
    switch (type) {
      case "price":
      case "stock":
      default:
        rules[value] = number()
          .typeError("Please enter a number")
          .required("Please enter a value")
          .min(0, "Please enter a value greater than 0");
        break;
      case "sku":
        rules[value] = string()
          .typeError("Please enter a value")
          .required("Please enter a value")
          .nullable();
        break;
    }
  });

  return object(rules);
};

/**
 * Returns dynamically generated validation rules for the 'all' settings method
 * @author Josh Smith <josh@batch.nz>
 * @param  string   type
 * @return object
 */
const getSettingsMethodAllRules = (type) => {
  switch (type) {
    case "price":
    case "stock":
    default:
      return object({
        value: number()
          .typeError("Please enter a number")
          .required("Please enter a value")
          .min(0, "Please enter a value greater than 0"),
      });
    case "sku":
      return object({
        value: string()
          .typeError("Please enter a number")
          .required("Please enter a value")
          .min(0, "Please enter a value greater than 0")
          .nullable(),
      });
  }
};

export const settingsStep = {
  component: "SettingsStep",
  header: {
    ...defaults.header,
    subtitle: "Step 4. Set the configuration settings",
  },
  footer: {
    ...defaults.footer,
    priBtnText: "Save Configuration",
  },
  rules: object().shape({
    settings: getSettingsTypeRules(),
  }),
};

export const generateStep = {
  component: "GenerateStep",
  header: {
    ...defaults.header,
    subtitle: "Step 5. Generate your variants",
  },
  footer: {
    ...defaults.footer,
    priBtnText: "Generate Variants",
  },
  rules: defaults.rules,
};

export default {
  indexStep,
  nameStep,
  fieldsStep,
  valuesStep,
  settingsStep,
  generateStep,
};
