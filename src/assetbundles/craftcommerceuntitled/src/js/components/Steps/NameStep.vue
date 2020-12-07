<template>
  <form class="field width-100">
    <div class="heading">
      <label for="configuration-name">Name</label>
    </div>
    <div id="configuration-name-instructions" class="instructions">
      <p>Give your configuration a name e.g. "Accent Colours"</p>
    </div>
    <div class="input ltr" :class="{ errors: errors.title }">
      <textarea
        @input="handleInput($event.target.value)"
        class="nicetext text"
        id="configuration-name"
        name="title"
        rows="1"
        cols="50"
        placeholder=""
        style="min-height: 32px"
        >{{ title }}</textarea
      >
      <ul class="errors" v-if="errors.title">
        <li>{{ errors.title }}</li>
      </ul>
    </div>
  </form>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
import { nameStep } from "../../store/stepState";
import { SET_VARIANT_CONFIGURATION_TITLE } from "../../constants/mutationTypes";

export default {
  computed: mapState({
    title: (state) => state.variantConfiguration.title,
    errors: (state) => state.formErrors,
  }),
  methods: {
    ...mapMutations({
      setTitle: SET_VARIANT_CONFIGURATION_TITLE,
    }),
    ...mapActions(["validate"]),
    async handleInput(value) {
      const { rules } = nameStep;
      if (await this.validate({ values: { title: value }, schema: rules })) {
        this.setTitle(value);
      }
    },
  },
};
</script>
