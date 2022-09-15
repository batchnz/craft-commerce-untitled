<template>
  <div class="field width-100">
    <div class="heading">
      <label for="configuration-fields"
        >Fields
        <a
          @click="toggleAllSelectedFields"
          href="#"
          style="font-size: 11px; font-weight: normal; margin-left: 2px"
          >{{ isAllFieldsSelected ? "Unselect all" : "Select all" }}</a
        >
      </label>
    </div>
    <div class="input ltr" :class="{ errors: errors.fields }">
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
      <ul class="errors" v-if="errors.fields">
        <li>{{ errors.fields }}</li>
      </ul>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations, mapGetters, mapActions } from "vuex";
import { fieldsStep } from "../../store/stepState";
import { SET_VARIANT_CONFIGURATION_FIELDS } from "../../constants/mutationTypes";

export default {
  computed: {
    ...mapState({
      fields: (state) => state.variantConfigurationTypeFields,
      errors: (state) => state.formErrors,
    }),
    ...mapGetters(["isAllFieldsSelected"]),
    variantFields: {
      get() {
        return this.$store.state.variantConfiguration.fields;
      },
      async set(val) {
        const { rules } = fieldsStep;
        await this.validate({ values: { fields: val }, schema: rules });
        this.setFields(val);
      },
    },
  },
  methods: {
    ...mapMutations({
      setFields: SET_VARIANT_CONFIGURATION_FIELDS,
    }),
    ...mapActions(["toggleAllSelectedFields", "validate"]),
  },
};
</script>
