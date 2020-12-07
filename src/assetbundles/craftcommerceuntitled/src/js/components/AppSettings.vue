<template>
  <div class="field">
    <div class="heading">
      <label>{{ settingsTitle }}</label>
    </div>
    <div class="input ltr">
      <fieldset>
        <div>
          <input
            type="radio"
            class="radio"
            :id="'settings-' + type + '-all'"
            :name="'settings[' + type + '][method]'"
            value="all"
            v-model="method"
          />
          <label :for="'settings-' + type + '-all'"
            >Set {{ type }} for all variants</label
          >
        </div>

        <!-- Set all values per field -->
        <div v-if="method === 'all'" class="field" style="margin-left: 32px">
          <div class="heading">
            <label>{{ settingsTitle }}</label>
          </div>
          <div class="input ltr">
            <!-- Prefix with a $ -->
            <div v-if="type === 'price'" class="flex">
              <div>$</div>
              <input
                type="text"
                class="nicetext text"
                @input="setFieldValue('value', $event.target.value)"
                :value="values['value']"
              />
            </div>
            <!-- Otherwise, just display a plain input -->
            <input
              v-else
              type="text"
              class="nicetext text"
              @input="setFieldValue('value', $event.target.value)"
              :value="values['value']"
            />
          </div>
          <ul class="errors" v-if="errors[`settings.${type}.values.value`]">
            <li>{{ errors[`settings.${type}.values.value`] }}</li>
          </ul>
        </div>

        <div>
          <input
            type="radio"
            class="radio"
            :id="'settings-' + type + '-field'"
            :name="'settings[' + type + '][method]'"
            value="field"
            v-model="method"
          />
          <label :for="'settings-' + type + '-field'"
            >Set {{ type }} per field</label
          >
        </div>

        <!-- Values per field -->
        <div v-if="method === 'field'" class="field" style="margin-left: 32px">
          <div class="heading">
            <label>Choose a field</label>
          </div>
          <div class="input ltr">
            <select name="" id="" v-model="field">
              <option :value="null">Select</option>
              <option
                v-for="field in selectedFields"
                v-if="selectedValuesByHandle[field.handle].length"
                :value="field.handle"
              >
                {{ field.name }}
              </option>
            </select>
          </div>
          <ul class="errors" v-if="errors[`settings.${type}.field`]">
            <li>{{ errors[`settings.${type}.field`] }}</li>
          </ul>
          <div
            v-if="field"
            v-for="{ label, value } in selectedValuesByHandle[field]"
            class="field"
          >
            <div class="heading">
              <label>{{ label }}</label>
            </div>
            <div class="input ltr">
              <!-- Prefix with a $ -->
              <div v-if="type === 'price'" class="flex">
                <div>$</div>
                <input
                  type="text"
                  class="nicetext text"
                  @input="setFieldValue(value, $event.target.value)"
                  :value="values[value]"
                />
              </div>
              <!-- Otherwise, just display a plain input -->
              <input
                v-else
                type="text"
                class="nicetext text"
                @input="setFieldValue(value, $event.target.value)"
                :value="values[value]"
              />
            </div>
            <ul
              class="errors"
              v-if="errors[`settings.${type}.values.${value}`]"
            >
              <li>{{ errors[`settings.${type}.values.${value}`] }}</li>
            </ul>
          </div>
        </div>

        <div>
          <input
            type="radio"
            class="radio"
            :id="'settings-' + type + '-skip'"
            :name="'settings[' + type + '][method]'"
            value="skip"
            v-model="method"
          />
          <label :for="'settings-' + type + '-skip'">Skip {{ type }}</label>
        </div>
      </fieldset>
      <ul class="errors" v-if="errors[`settings.${type}.method`]">
        <li>{{ errors[`settings.${type}.method`] }}</li>
      </ul>
    </div>
  </div>
</template>

<script>
import * as SETTINGS from "../constants/settingsTypes";
import { mapMutations, mapGetters, mapState } from "vuex";

export default {
  props: {
    type: {
      type: String,
      required: true,
    },
  },
  computed: {
    ...mapState({
      variantSettings(state) {
        return (
          state.variantConfiguration.settings[this.type] || this.defaultSettings
        );
      },
      errors: (state) => state.formErrors,
    }),
    ...mapGetters({
      defaultSettings: "defaultSettings",
      selectedFields: "selectedFields",
      selectedValuesByHandle: "selectedFieldValuesByHandle",
    }),
    settingsTitle() {
      return this.type.charAt(0).toUpperCase() + this.type.slice(1);
    },
    [SETTINGS.METHOD]: {
      get() {
        return this.variantSettings[SETTINGS.METHOD];
      },
      async set(val) {
        const settings = {
          ...this.variantSettings,
          ...{ [SETTINGS.METHOD]: val },
        };
        this.setSettingsType({ type: this.type, data: settings });
      },
    },
    [SETTINGS.FIELD]: {
      get() {
        return this.variantSettings[SETTINGS.FIELD];
      },
      async set(val) {
        const settings = {
          ...this.variantSettings,
          ...{ [SETTINGS.FIELD]: val },
        };
        this.setSettingsType({ type: this.type, data: settings });
      },
    },
    [SETTINGS.VALUES]: {
      get() {
        return this.variantSettings[SETTINGS.VALUES];
      },
      async set(val) {
        const settings = {
          ...this.variantSettings,
          ...{ [SETTINGS.VALUES]: val },
        };
        this.setSettingsType({ type: this.type, data: settings });
      },
    },
  },
  methods: {
    ...mapMutations({
      setSettingsType: "SET_VARIANT_CONFIGURATION_SETTINGS_TYPE",
    }),
    setFieldValue(fieldId, val) {
      const value = {};
      const values = this.variantSettings[SETTINGS.VALUES];

      // Add/Remove values
      if (val == null || val == "") {
        delete values[fieldId];
      } else {
        value[fieldId] = val;
      }

      const settings = {
        ...this.variantSettings,
        ...{ [SETTINGS.VALUES]: { ...values, ...value } },
      };
      this.setSettingsType({ type: this.type, data: settings });
    },
  },
};
</script>
