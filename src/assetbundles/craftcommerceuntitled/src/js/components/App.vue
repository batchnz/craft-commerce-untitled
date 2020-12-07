<template>
  <div>
    <div class="body">
      <div class="content-summary">
        <AppHeader
          :title="state.header.title"
          :subtitle="state.header.subtitle"
        />
      </div>
      <AppLoading v-if="isLoading" />
      <component v-if="!isLoading" :is="state.component" />
    </div>
    <AppFooter
      :isLoading="isLoading"
      :priBtnText="state.footer.priBtnText"
      :secBtnText="state.footer.secBtnText"
    />
  </div>
</template>

<script>
import AppLoading from "./AppLoading";
import AppHeader from "./AppHeader";
import AppFooter from "./AppFooter";
import {
  IndexStep,
  NameStep,
  FieldsStep,
  ValuesStep,
  SettingsStep,
} from "./Steps";

import { mapActions, mapState, mapMutations, mapGetters } from "vuex";

export default {
  components: {
    AppLoading,
    AppHeader,
    AppFooter,
    IndexStep,
    NameStep,
    FieldsStep,
    ValuesStep,
    SettingsStep,
  },
  props: {
    productId: null,
    productTypeId: null,
  },
  computed: {
    ...mapState({
      isLoading: (state) => state.ui.isLoading,
    }),
    ...mapGetters({
      state: "currentStepState",
    }),
  },
  async created() {
    this.setIsLoading(true);

    await this.fetchVariantConfigurations({
      productId: this.productId,
    });

    await this.fetchVariantConfigurationTypeFields({
      productTypeId: this.productTypeId,
    });

    this.setIsLoading(false);
  },
  methods: {
    ...mapActions([
      "fetchVariantConfigurations",
      "fetchVariantConfigurationTypeFields",
    ]),

    ...mapMutations({ setIsLoading: "SET_IS_LOADING" }),
  },
};
</script>
