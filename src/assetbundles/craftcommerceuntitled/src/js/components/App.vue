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
      <component v-if="!isLoading" :is="state.currentStepComponent" />
    </div>
    <AppFooter
      :isLoading="isLoading"
      :priBtnText="state.footer.priBtnText"
      :secBtnText="state.footer.secBtnText"
      @priBtnClick="navigate(step + 1)"
      @secBtnClick="navigate(step - 1)"
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

import { mapActions, mapState, mapMutations } from "vuex";

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
  data() {
    return {
      step: 0,
      totalSteps: 5,
      state: {},
      defaults: {
        header: {
          title: "Variant Configurator",
          subtitle: "Select or create a variant configuration",
        },
        footer: {
          priBtnText: "Next Step",
          secBtnText: "Previous",
        },
      },
    };
  },
  computed: mapState({
    isLoading: (state) => state.ui.isLoading,
  }),
  watch: {
    step() {
      this.state = this.getState();
    },
  },
  async created() {
    this.setIsLoading(true);

    this.state = this.getState();

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
    /**
     * Navigate between form steps
     * @author Josh Smith <josh@batch.nz>
     * @param  int  step
     * @return void
     */
    navigate(step) {
      if (step < 0) {
        return self.hide();
      }
      if (step >= this.totalSteps) {
        return;
      }
      this.step = step;
    },

    /**
     * Returns the application state for each form step
     * @author Josh Smith <josh@batch.nz>
     * @return object
     */
    getState() {
      switch (this.step) {
        case 0:
        default:
          return {
            currentStepComponent: "IndexStep",
            header: this.defaults.header,
            footer: {
              priBtnText: "Create New",
              secBtnText: "Cancel",
            },
          };

        case 1:
          return {
            currentStepComponent: "NameStep",
            header: {
              ...this.defaults.header,
              subtitle: "Step 1. Set the configuration name",
            },
            footer: this.defaults.footer,
          };

        case 2:
          return {
            currentStepComponent: "FieldsStep",
            header: {
              ...this.defaults.header,
              subtitle: "Step 2. Select the fields to generate variants from",
            },
            footer: this.defaults.footer,
          };

        case 3:
          return {
            currentStepComponent: "ValuesStep",
            header: {
              ...this.defaults.header,
              subtitle: "Step 3. Select the values to generate variants from",
            },
            footer: this.defaults.footer,
          };

        case 4:
          return {
            currentStepComponent: "SettingsStep",
            header: {
              ...this.defaults.header,
              subtitle: "Step 4. Set the configuration settings",
            },
            footer: {
              ...this.defaults.footer,
              priBtnText: "Save Configuration",
            },
          };
      }
    },
  },
};
</script>
