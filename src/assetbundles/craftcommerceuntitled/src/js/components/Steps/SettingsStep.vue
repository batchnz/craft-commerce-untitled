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
import eventBus from "../../store/eventBus";
import AppSettings from "../AppSettings";
import { TYPES } from "../../constants/settingsTypes";

export default {
  created() {
    eventBus.on("form-submission", (cb) => {
      cb(async () => {
        return await this.saveVariantConfiguration();
      });
    });
  },
  computed: {
    types() {
      return TYPES;
    },
  },
  methods: {
    ...mapActions(["saveVariantConfiguration"]),
  },
  components: {
    AppSettings,
  },
};
</script>
