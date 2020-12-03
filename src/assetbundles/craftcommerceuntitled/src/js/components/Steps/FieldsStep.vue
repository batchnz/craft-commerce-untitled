<template>
  <div class="field width-100">
    <div class="heading">
      <label for="configuration-fields"
        >Fields
        <a
          @click="toggleAllSelectedFields"
          href="#"
          style="font-size: 11px; font-weight: normal; margin-left: 2px"
          >{{ isAllSelected ? "Unselect all" : "Select all" }}</a
        >
      </label>
    </div>
    <div class="input ltr">
      <fieldset class="checkbox-group">
        <BaseCheckbox
          class="input-list"
          name="fields[]"
          v-model="variantFields"
          :options="variantFieldCheckboxes"
        />
      </fieldset>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations, mapGetters, mapActions } from "vuex";
import BaseCheckbox from "../BaseCheckbox";

export default {
  components: { BaseCheckbox },
  computed: {
    ...mapState({
      fields: (state) => state.variantConfigurationTypeFields,
    }),
    ...mapGetters({ isAllSelected: "isAllFieldsSelected" }),
    variantFields: {
      get() {
        return this.$store.state.variantConfiguration.fields;
      },
      set(val) {
        this.setVariantConfigurationFields(val);
      },
    },
    variantFieldCheckboxes() {
      return this.fields.map((field) => ({
        label: field.name,
        value: field.handle,
        id: "fields-" + field.handle,
      }));
    },
  },
  methods: {
    ...mapMutations({
      setVariantConfigurationFields: "SET_VARIANT_CONFIGURATION_FIELDS",
    }),
    ...mapActions(["toggleAllSelectedFields"]),
  },
};
</script>
