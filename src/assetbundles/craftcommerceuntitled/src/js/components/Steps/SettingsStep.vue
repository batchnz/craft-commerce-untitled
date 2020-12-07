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
import AppSettings from "../AppSettings";
import { TYPES } from "../../constants/settingsTypes";

export default {
  components: {
    AppSettings,
  },
  computed: {
    types() {
      return TYPES;
    },
  },
};
</script>
