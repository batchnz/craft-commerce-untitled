<template>
  <div class="field width-100">
    <div class="heading">
      <label for="configuration-name">Name</label>
    </div>
    <div id="configuration-name-instructions" class="instructions">
      <p>Give your configuration a name e.g. "Accent Colours"</p>
    </div>
    <div class="input ltr">
      <textarea
        @input="$emit('input', $event.target.value)"
        id="configuration-name"
        class="nicetext text"
        name="title"
        rows="1"
        cols="50"
        placeholder=""
        style="min-height: 32px"
        >{{ value }}</textarea
      >
    </div>
  </div>
</template>

<script>
export default {
  props: {
    value: String,
  },
};
</script>
