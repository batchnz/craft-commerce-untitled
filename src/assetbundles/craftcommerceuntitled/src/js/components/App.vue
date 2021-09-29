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
      <component v-if="!isLoading" :is="state.component" v-bind="{hasDimensions: hasDimensions}"/>
    </div>
    <AppFooter
      :isLoading="isLoading"
      :isSubmitting="isSubmitting"
      :isCompleted="isCompleted"
      :priBtnText="state.footer.priBtnText"
      :secBtnText="state.footer.secBtnText"
      :step="step"
      @close-modal="handleCloseModal"
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
  GenerateStep,
} from "./Steps";

import { mapActions, mapState, mapMutations, mapGetters } from "vuex";
import * as MUTATIONS from "../constants/mutationTypes";

export default {
  props: {
    productId: Number,
    productTypeId: Number,
    variantConfigurationTypeId: Number,
    $modal: Object,
    hasDimensions: Number,
  },
  computed: {
    ...mapState({
      isLoading: (state) => state.ui.isLoading,
      isSubmitting: (state) => state.ui.isSubmitting,
      isCompleted: (state) => state.ui.isCompleted,
      step: (state) => state.step,
    }),
    ...mapGetters({
      state: "currentStepState",
    }),
  },
  async created() {
    this.setIsLoading(true);

    // Commit initial ID values to the store
    this.setProductId(this.productId);
    this.setProductTypeId(this.productTypeId);
    this.setTypeId(this.variantConfigurationTypeId);
    this.setHasDimensions(this.hasDimensions);

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
    ...mapMutations({
      setIsLoading: MUTATIONS.SET_IS_LOADING,
      setProductId: MUTATIONS.SET_PRODUCT_ID,
      setProductTypeId: MUTATIONS.SET_PRODUCT_TYPE_ID,
      setTypeId: MUTATIONS.SET_TYPE_ID,
      setHasDimensions: MUTATIONS.SET_HAS_DIMENSIONS,
    }),
    handleCloseModal() {
      this.$modal.hide();
    },
  },
  components: {
    AppLoading,
    AppHeader,
    AppFooter,
    IndexStep,
    NameStep,
    FieldsStep,
    ValuesStep,
    SettingsStep,
    GenerateStep,
  },
};
</script>
