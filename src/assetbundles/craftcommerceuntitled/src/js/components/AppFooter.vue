<template>
  <div class="footer">
    <div class="buttons left" v-if="step > 0">
      <button @click="menuStep" class="btn icon nav">Menu</button>
    </div>
    <div v-if="!isCompleted" class="buttons right">
      <button @click="handleSecBtnClick" class="btn">
        {{ secBtnText }}
      </button>
      <button
        @click="nextStep"
        :disabled="isLoading || isSubmitting"
        :class="{
          disabled: isLoading,
          add: isSubmitting,
          icon: isSubmitting,
          loading: isSubmitting,
        }"
        class="btn submit"
      >
        {{ priBtnText }}
      </button>
    </div>
    <div v-else class="buttons right">
      <button @click="$emit('close-modal')" class="btn submit">Finish</button>
    </div>
  </div>
</template>

<script>
import { mapActions } from "vuex";
export default {
  props: {
    isLoading: Boolean,
    isSubmitting: Boolean,
    isCompleted: Boolean,
    priBtnText: String,
    secBtnText: String,
    step: Number,
  },
  methods: {
    ...mapActions(["nextStep", "prevStep", "menuStep"]),
    handleSecBtnClick() {
      if (this.step === 0) {
        this.$emit("close-modal");
      } else {
        this.prevStep();
      }
    },
  },
};
</script>
