<template>
  <div class="footer">
    <div class="buttons right">
      <button @click="$emit('secBtnClick')" class="btn">
        {{ secBtnText }}
      </button>
      <button
        @click="$emit('priBtnClick')"
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
export default {
  props: {
    isLoading: Boolean,
    priBtnText: String,
    secBtnText: String,
  },
};
</script>
