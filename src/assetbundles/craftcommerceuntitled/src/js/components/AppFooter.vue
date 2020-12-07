<template>
  <div class="footer">
    <div class="buttons right">
      <button @click="prevStep" class="btn">
        {{ secBtnText }}
      </button>
      <button
        @click="nextStep"
        :disabled="isLoading"
        :class="{ disabled: isLoading }"
        class="btn submit"
      >
        {{ priBtnText }}
      </button>
    </div>
  </div>
</template>

<script>
import { mapActions } from "vuex";
export default {
  props: {
    isLoading: Boolean,
    priBtnText: String,
    secBtnText: String,
  },
  methods: mapActions(["nextStep", "prevStep"]),
};
</script>
