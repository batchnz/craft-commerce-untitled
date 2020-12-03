<template>
  <FormulateInput
    type="checkbox"
    v-model="model"
    v-bind="{ ...$props, ...$attrs }"
    :options="decorate(options)"
  />
</template>

<script>
export default {
  props: ["options", "value"],
  computed: {
    model: {
      get() {
        return this.value;
      },
      set(val) {
        this.$emit("input", val);
      },
    },
  },
  inheritAttrs: false,
  methods: {
    decorate(options) {
      return options.map((option) => ({
        ...option,
        ...{
          "wrapper-class": "flex flex-nowrap",
          "input-class": (context, classes) => ["checkbox"].concat(classes),
          "input-wrapper-class": (context, classes) =>
            ["w-full"].concat(classes),
          "outer-class": "w-1/4",
        },
      }));
    },
  },
};
</script>
