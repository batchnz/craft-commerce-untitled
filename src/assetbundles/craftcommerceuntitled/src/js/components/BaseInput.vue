<template>
  <FormulateInput
    type="text"
    v-bind="{ ...$props, ...$attrs }"
    @input="$emit('input', $event)"
    :wrapper-class="['field', 'width-100']"
    :element-class="(context, classes) => ['input', 'ltr'].concat(classes)"
    :input-class="['nicetext', 'text']"
  >
    <template #label="{ label, id }">
      <div class="heading">
        <label :for="id">
          {{ label }}
        </label>
      </div>
      <div v-if="instructions" class="instructions">
        {{ instructions }}
      </div>
    </template>
  </FormulateInput>
</template>

<script>
export default {
  props: {
    instructions: String,
  },
};
</script>
