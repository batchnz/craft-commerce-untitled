<template>
  <div>
    <div v-for="(type, i) in types">
      <AppSettings :type="type" />
      <hr v-if="i < types.length - 1" />
    </div>
  </div>
</template>

<script>
import Vue from "vue";
import { mapActions } from "vuex";
import AsyncEventBus from "../../store/asyncEventBus";
import AppSettings from "../AppSettings";
import { TYPES } from "../../constants/settingsTypes";

export default {
  created() {
    AsyncEventBus.once("form-submission", this.saveVariantConfiguration);
  },
  computed: {
    types() {
      return TYPES;
    },
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
