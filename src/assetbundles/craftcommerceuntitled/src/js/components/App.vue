<template>
  <div>
    <div class="body">
      <div class="content-summary">
        <Header :title="state.header.title" :subtitle="state.header.subtitle" />
      </div>
      <Loading v-if="isLoading" />
      <component
        v-if="!isLoading"
        v-model="variantConfiguration[state.currentStepKey]"
        :is="state.currentStepComponent"
        :vc="variantConfiguration"
        :configs="variantConfigurations"
        :fields="variantConfigurationTypeFields"
      />
    </div>
    <Footer
      :isLoading="isLoading"
      :priBtnText="state.footer.priBtnText"
      :secBtnText="state.footer.secBtnText"
      @priBtnClick="navigate(step + 1)"
      @secBtnClick="navigate(step - 1)"
    />
  </div>
</template>

<script>
import Loading from "./Loading";
import Header from "./Header";
import Footer from "./Footer";
import {
  IndexStep,
  NameStep,
  FieldsStep,
  ValuesStep,
  SettingsStep,
} from "./Steps";

export default {
  data() {
    return {
      step: 4,
      totalSteps: 5,
      variantConfigurations: [],
      variantConfigurationTypeFields: [],
      isLoading: true,
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
      variantConfiguration: {
        id: null,
        title: "",
        fields: ["paintColour"],
        values: [5089, 5090, 5091],
        settings: {},
      },
    };
  },
  async created() {
    this.isLoading = true;
    this.state = this.getState();

    // Load configurations
    const vcRes = await self.getVariantConfigurations({
      productId: self.settings.productId,
    });
    this.variantConfigurations = vcRes.data;

    // Load fields
    const vcTypeFieldsRes = await self.getVariantConfigurationTypeFields({
      productTypeId: self.settings.productTypeId,
    });
    this.variantConfigurationTypeFields = vcTypeFieldsRes.data;

    this.isLoading = false;
  },
  methods: {
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
            currentStepKey: this.variantConfiguration.title,
            header: {
              ...this.defaults.header,
              subtitle: "Step 1. Set the configuration name",
            },
            footer: this.defaults.footer,
          };

        case 2:
          return {
            currentStepComponent: "FieldsStep",
            currentStepKey: "fields",
            header: {
              ...this.defaults.header,
              subtitle: "Step 2. Select the fields to generate variants from",
            },
            footer: this.defaults.footer,
          };

        case 3:
          return {
            currentStepComponent: "ValuesStep",
            currentStepKey: "values",
            header: {
              ...this.defaults.header,
              subtitle: "Step 3. Select the values to generate variants from",
            },
            footer: this.defaults.footer,
          };

        case 4:
          return {
            currentStepComponent: "SettingsStep",
            currentStepKey: "settings",
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
  watch: {
    step() {
      this.state = this.getState();
    },
  },
  components: {
    Loading,
    Header,
    Footer,
    IndexStep,
    NameStep,
    FieldsStep,
    ValuesStep,
    SettingsStep,
  },
};
</script>
