<template>
  <div class="field width-100">
    <div class="heading">
      <label for="configuration-fields"
        >Fields
        <a
          @click="toggleSelectAll"
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
            v-model="model"
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
export default {
  props: {
    fields: Array,
    value: Array,
  },
  data() {
    return {
      model: this.value,
    };
  },
  methods: {
    toggleSelectAll() {
      if (this.isAllSelected) {
        return (this.model = []);
      }

      this.model = [];
      this.fields.forEach((field) => {
        this.model.push(field.handle);
      });
    },
  },
  computed: {
    isAllSelected() {
      const handles = this.fields.map((field) => field.handle);
      const filteredArray = handles.filter((value) =>
        this.model.includes(value)
      );

      return filteredArray.length === handles.length;
    },
  },
  watch: {
    model: function (val) {
      this.$emit("input", val);
    },
  },
};
</script>
