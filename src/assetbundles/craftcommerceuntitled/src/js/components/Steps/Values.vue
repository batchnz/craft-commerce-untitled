<template>
  <div>
    <div v-for="field in availableFields" class="field width-100">
      <div class="heading">
        <label for="configuration-fields"
          >{{ field.name }}
          <a
            @click="toggleSelectAll(field.handle)"
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
              v-model="model"
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
export default {
  props: {
    fields: Array,
    value: Array,
    vc: Object,
  },
  data() {
    return {
      model: this.value,
    };
  },
  methods: {
    toggleSelectAll(handle) {
      const values = this.valuesByHandle[handle];
      if (!values.length) return;

      if (!this.isAllSelected(handle)) {
        this.model = [...this.model, ...values];
      } else {
        this.model = this.model.filter((value) => values.indexOf(value) === -1);
      }
    },
    isAllSelected(handle) {
      const values = this.valuesByHandle[handle];

      const filteredArray = values.filter((value) =>
        this.model.includes(value)
      );

      return filteredArray.length === values.length;
    },
  },
  computed: {
    availableFields() {
      return this.fields.filter((field) =>
        this.vc.fields.includes(field.handle)
      );
    },
    valuesByHandle() {
      const values = {};
      this.fields.forEach((field) => {
        values[field.handle] = field.values.map(({ value }) => value);
      });
      return values;
    },
  },
  watch: {
    model: function (val) {
      this.$emit("input", val);
    },
  },
};
</script>
