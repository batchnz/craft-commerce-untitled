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
        <div v-for="field in fields">
          <input
            type="checkbox"
            class="checkbox"
            name="fields[]"
            :id="'fields-' + field.handle"
            :value="field.handle"
            v-model="variantFields"
          />
          <label :for="'fields-' + field.handle">
            {{ field.name }}
          </label>
        </div>
      </fieldset>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations, mapGetters, mapActions } from "vuex";

export default {
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
  },
  methods: {
    ...mapMutations({
      setVariantConfigurationFields: "SET_VARIANT_CONFIGURATION_FIELDS",
    }),
    ...mapActions(["toggleAllSelectedFields"]),
  },
};
</script>
