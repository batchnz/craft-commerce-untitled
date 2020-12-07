<template>
  <div>
    <div v-for="field in selectedFields" class="field width-100">
      <div class="heading">
        <label for="configuration-fields"
          >{{ field.name }}
          <a
            @click="handleToggleValues(field.handle)"
            href="#"
            style="font-size: 11px; font-weight: normal; margin-left: 2px"
            >{{
              isAllSelected(field.handle) ? "Unselect all" : "Select all"
            }}</a
          >
        </label>
      </div>
      <div class="input ltr" :class="{ errors: errors.values }">
        <fieldset class="checkbox-group" style="display: flex; flex-wrap: wrap">
          <div v-for="{ label, value } in field.values" style="width: 25%">
            <input
              type="checkbox"
              :id="'fields-' + value"
              class="checkbox"
              name="fields[]"
              :value="value"
              v-model="values"
            />
            <label :for="'fields-' + value">
              {{ label }}
            </label>
          </div>
        </fieldset>
        <ul class="errors" v-if="errors.values">
          <li>{{ errors.values }}</li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapMutations, mapState, mapGetters } from "vuex";
import { valuesStep } from "../../store/stepState";

export default {
  computed: {
    ...mapState({
      fields: (state) => state.variantConfigurationTypeFields,
      errors: (state) => state.formErrors,
      variantValues: (state) => state.variantConfiguration.values,
      variantFields: (state) => state.variantConfiguration.fields,
    }),
    ...mapGetters({
      selectedFields: "selectedFields",
      isAllSelected: "isAllValuesSelected",
    }),
    values: {
      get() {
        return this.variantValues;
      },
      async set(values) {
        await this.isValid(values);
        this.setValues(values);
      },
    },
  },
  methods: {
    ...mapActions(["toggleAllSelectedValues", "validate"]),
    ...mapMutations({
      setValues: "SET_VARIANT_CONFIGURATION_VALUES",
    }),
    handleToggleValues(fieldHandle) {
      this.toggleAllSelectedValues(fieldHandle);
      this.isValid(this.variantValues);
    },
    async isValid(values) {
      const { rules } = valuesStep;
      return await this.validate({ values: { values }, schema: rules });
    },
  },
};
</script>
