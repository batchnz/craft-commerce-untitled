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
        @input="setTitle($event.target.value)"
        id="configuration-name"
        class="nicetext text"
        name="title"
        rows="1"
        cols="50"
        placeholder=""
        style="min-height: 32px"
        >{{ title }}</textarea
      >
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations } from "vuex";
export default {
  computed: mapState({
    title: (state) => state.variantConfiguration.title,
  }),
  methods: mapMutations({
    setTitle: "SET_VARIANT_CONFIGURATION_TITLE",
  }),
};
</script>
