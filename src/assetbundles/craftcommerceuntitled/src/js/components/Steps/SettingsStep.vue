<template>
  <div>
    <div v-for="(type, i) in types" class="field width-100">
      <div class="heading">
        <label>{{ type.charAt(0).toUpperCase() + type.slice(1) }}</label>
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
              v-model="model[type].method"
            />
            <label :for="'settings-' + type + '-all'"
              >Set {{ type }} for all variants</label
            >
          </div>

          <div>
            <input
              type="radio"
              class="radio"
              :id="'settings-' + type + '-field'"
              :name="'settings[' + type + '][method]'"
              value="field"
              v-model="model[type].method"
            />
            <label :for="'settings-' + type + '-field'"
              >Set {{ type }} per field</label
            >
          </div>

          <!-- Values per field -->
          <div
            v-if="model[type].method === 'field'"
            class="field"
            style="margin-left: 32px"
          >
            <div class="heading">
              <label>Choose a field</label>
            </div>
            <div class="input ltr">
              <select name="" id="" v-model="model[type].field">
                <option :value="null">Select</option>
                <option v-for="field in availableFields" :value="field.handle">
                  {{ field.name }}
                </option>
              </select>
            </div>
            <div
              v-if="model[type].field"
              v-for="{ label, value } in availableValuesByHandle[
                model[type].field
              ]"
              class="field"
            >
              <div class="heading">
                <label>{{ label }}</label>
              </div>
              <div class="input ltr">
                <input type="text" v-model="model[type].values[value]" />
              </div>
            </div>
          </div>

          <div>
            <input
              type="radio"
              class="radio"
              :id="'settings-' + type + '-skip'"
              :name="'settings[' + type + '][method]'"
              value="skip"
              v-model="model[type].method"
            />
            <label :for="'settings-' + type + '-skip'">Skip {{ type }}</label>
          </div>
        </fieldset>
      </div>
      <hr v-if="i < types.length - 1" />
    </div>
  </div>
</template>

<script>
import Vue from "vue";

export default {
  props: {
    value: Object,
    fields: Array,
    vc: Object,
  },
  data() {
    return {
      types: ["price", "stock"],
      model: {},
    };
  },
  created() {
    this.types.forEach((type) => {
      Vue.set(
        this.model,
        type,
        this.value[type] || {
          field: null,
          method: null,
          values: {},
        }
      );
    });
  },
  computed: {
    availableFields() {
      return this.fields.filter((field) =>
        this.vc.fields.includes(field.handle)
      );
    },
    availableValuesByHandle() {
      const values = {};

      // Determine what fields have been selected
      const fields = this.fields.filter((field) =>
        this.vc.fields.includes(field.handle)
      );

      // Loop the fields and determine the available values
      fields.forEach((field) => {
        values[field.handle] = [];
        field.values.forEach((fieldVal) => {
          if (this.vc.values.includes(fieldVal.value)) {
            values[field.handle].push(fieldVal);
          }
        });
      });

      return values;
    },
  },
  watch: {
    model: {
      handler(val) {
        this.$emit("input", val);
      },
      deep: true,
    },
  },
};
</script>
