<template>
  <div>
    <div v-for="(type, i) in allowedTypes">
      <AppSettings :type="type" :hasDimensions="hasDimensions" />
      <hr v-if="i < allowedTypes.length - 1" />
    </div>
  </div>
</template>

<script>
import Vue from "vue";
import { mapActions, mapGetters } from "vuex";
import AsyncEventBus from "../../store/asyncEventBus";
import AppSettings from "../AppSettings";

export default {
  props: {
    hasDimensions: {
      type: Boolean,
      required: true,
    }
  },

  created() {
    AsyncEventBus.once("form-submission", this.saveVariantConfiguration);
  },
  computed: {
    ...mapGetters(['allowedTypes'])
  },
  methods: {
    ...mapActions(["saveVariantConfiguration"]),
  },
  beforeDestroy() {
    AsyncEventBus.off("form-submission", this.saveVariantConfiguration);
  },
  components: {
    AppSettings,
  },
};
</script>
