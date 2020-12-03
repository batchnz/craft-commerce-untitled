<template>
  <div class="field width-100">
    <BaseTextarea
      label="Name"
      instructions='Give your configuration a name e.g. "Accent Colours"'
      @input="setTitle($event)"
      :value="title"
    />
  </div>
</template>

<script>
import { mapState, mapMutations } from "vuex";
import BaseTextarea from "../BaseTextarea";
export default {
  components: { BaseTextarea },
  computed: mapState({
    title: (state) => state.variantConfiguration.title,
  }),
  methods: mapMutations({
    setTitle: "SET_VARIANT_CONFIGURATION_TITLE",
  }),
};
</script>
