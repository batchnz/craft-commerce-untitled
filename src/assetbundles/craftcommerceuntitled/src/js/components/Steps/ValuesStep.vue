<template>
  <div>
    <div v-for="field in selectedFields" class="field width-100">
      <div class="heading">
        <label for="configuration-fields"
          >{{ field.name }}
          <a
            @click="toggleAllSelectedValues(field.handle)"
            href="#"
            style="font-size: 11px; font-weight: normal; margin-left: 2px"
            >{{
              isAllSelected(field.handle) ? "Unselect all" : "Select all"
            }}</a
          >
        </label>
      </div>
      <div class="input ltr">
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
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapMutations, mapState, mapGetters } from "vuex";

export default {
  computed: {
    ...mapState({
      fields: (state) => state.variantConfigurationTypeFields,
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
      set(val) {
        this.setValues(val);
      },
    },
  },
  methods: {
    ...mapActions(["toggleAllSelectedValues"]),
    ...mapMutations({
      setValues: "SET_VARIANT_CONFIGURATION_VALUES",
    }),
  },
};
</script>
