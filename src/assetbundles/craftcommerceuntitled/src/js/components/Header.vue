<template>
  <div>
    <h1 style="font-size: 20px; font-weight: bold">
      {{ title }}
    </h1>
    <p style="margin-bottom: 2rem !important">
      {{ subtitle }}
    </p>
  </div>
</template>
<script>
export default {
  props: {
    title: String,
    subtitle: String,
  },
};
</script>
