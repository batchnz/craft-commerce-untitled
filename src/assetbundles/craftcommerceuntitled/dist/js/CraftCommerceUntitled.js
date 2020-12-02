/**
 * Craft Commerce Untitled plugin for Craft CMS
 *
 * Craft Commerce Untitled JS
 *
 * @author    Josh Smith <hey@joshthe.dev>
 * @copyright Copyright (c) 2020 Josh Smith
 * @link      https://www.batch.nz
 * @package   CraftCommerceUntitled
 * @since     1.0.0
 */
(function ($) {
  /**
   * The main plugin object
   * Responsible for setting up the variant configuration interactivity
   * @author Josh Smith <hey@joshthe.dev>
   */
  Craft.CommerceUntitled = Garnish.Base.extend({
    settings: {
      productId: null,
      productTypeId: null,
    },

    /**
     * Initialises this object
     * @author Josh Smith <hey@joshthe.dev>
     * @param  object settings
     * @return void
     */
    init(settings) {
      const self = this;
      this.settings = $.extend({}, Craft.CommerceUntitled.defaults, settings);
      this.initEditBtn();
    },

    /**
     * Intialises the Edit Configurations button
     * @author Josh Smith <hey@joshthe.dev>
     * @return void
     */
    initEditBtn() {
      const $editBtn = $(this.getEditBtnHtml());
      $("#header").find(".btngroup").before($editBtn);

      // Attach event handler to trigger the configuration modal
      $editBtn.on("click", (e) => {
        e.preventDefault();
        new Craft.VariantConfigurationModal({
          productId: this.settings.productId,
          productTypeId: this.settings.productTypeId,
        });
      });
    },

    /**
     * Returns the HTML for the edit button
     * @author Josh Smith <hey@joshthe.dev>
     * @return string
     */
    getEditBtnHtml() {
      return '<button class="btn secondary">Edit Configurations</button>';
    },
  });

  /**
   * Static method that returns an API url
   * @author Josh Smith <josh@batch.nz>
   * @param  string slug
   * @return string
   */
  Craft.CommerceUntitled.getApiUrl = (slug = "", params = {}) => {
    // Build query string params
    const qs = new URLSearchParams(params).toString();

    // Assemble the api url with query string params
    return `${Craft.getSiteUrl() + Craft.CommerceUntitled.pluginHandle}/api/${
      Craft.CommerceUntitled.apiVersion
    }/${slug + (qs.length > 0 ? `?${qs}` : ``)}`;
  };

  /**
   * Variant Configuration Modal Object
   * @type Garnish.Modal
   */
  Craft.VariantConfigurationModal = Garnish.Modal.extend({
    settings: {
      productId: null,
      productTypeId: null,
    },
    closeOtherModals: true,
    shadeClass: "modal-shade dark",

    init(settings) {
      const self = this;
      this.settings = $.extend(
        {},
        Craft.VariantConfigurationModal.defaults,
        settings
      );

      // Register Vue components
      this.registerComponents();

      // Append the root element
      this.$container = $(
        `<div class="modal elementselectormodal">
          <div id="variant-configuration-app" />
        </div>`
      ).appendTo(Garnish.$bod);

      const vm = new Vue({
        el: "#variant-configuration-app",
        data: {
          step: 2,
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
            values: [],
            settings: {},
          },
        },
        async created() {
          this.isLoading = true;
          this.state = this.getStateMachine();

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
          getStateMachine() {
            switch (this.step) {
              case 0:
              default:
                return {
                  currentStepComponent: "VCIndexStep",
                  header: this.defaults.header,
                  footer: {
                    priBtnText: "Create New",
                    secBtnText: "Cancel",
                  },
                };

              case 1:
                return {
                  currentStepComponent: "VCNameStep",
                  currentStepKey: this.variantConfiguration.title,
                  header: {
                    ...this.defaults.header,
                    subtitle: "Step 1. Set the configuration name",
                  },
                  footer: this.defaults.footer,
                };

              case 2:
                return {
                  currentStepComponent: "VCFieldsStep",
                  currentStepKey: "fields",
                  header: {
                    ...this.defaults.header,
                    subtitle:
                      "Step 2. Select the fields to generate variants from",
                  },
                  footer: this.defaults.footer,
                };

              case 3:
                return {
                  currentStepComponent: "VCValuesStep",
                  currentStepKey: "values",
                  header: {
                    ...this.defaults.header,
                    subtitle:
                      "Step 3. Select the values to generate variants from",
                  },
                  footer: this.defaults.footer,
                };

              case 4:
                return {
                  currentStepComponent: "VCSettingsStep",
                  currentStepKey: "settings",
                  header: {
                    ...this.defaults.header,
                    subtitle: "Step 4. Set the configuration settings",
                  },
                  footer: this.defaults.footer,
                };
            }
          },
        },
        watch: {
          step() {
            this.state = this.getStateMachine();
          },
        },
        template: `
          <div>
            <div class="body">
              <div class="content-summary">
                <VCHeader
                  :title="state.header.title"
                  :subtitle="state.header.subtitle"
                />
              </div>
              <VCLoading v-if="isLoading" />
              <component
                v-if="!isLoading"
                v-model="variantConfiguration[state.currentStepKey]"
                :is="state.currentStepComponent"
                :vc="variantConfigurations"
                :vcFields="variantConfigurationTypeFields"
              />
            </div>
            <VCFooter
              :isLoading="isLoading"
              :priBtnText="state.footer.priBtnText"
              :secBtnText="state.footer.secBtnText"
              @priBtnClick="navigate(step+1)"
              @secBtnClick="navigate(step-1)"
            />
          </div>
        `,
      });

      this.base(this.$container, settings);
    },

    /**
     * Registers Vue components
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    registerComponents() {
      const self = this;

      Vue.component("VCHeader", {
        props: {
          title: String,
          subtitle: String,
        },
        template: `
          <div>
            <h1 style="font-size: 20px;font-weight: bold;">
              {{title}}
            </h1>
            <p style="margin-bottom: 2rem !important;">
              {{subtitle}}
            </p>
          </div>
        `,
      });

      Vue.component("VCIndexStep", {
        data() {
          return {
            dt: null,
          };
        },
        mounted() {
          this.dt = $(this.$refs.table).DataTable({ data: this.dtData });
        },
        beforeDestroy() {
          this.dt.destroy();
        },
        props: {
          vc: {
            type: Array,
            default() {
              return [];
            },
          },
        },
        computed: {
          dtData() {
            const data = [];
            this.vc.forEach((vc) => {
              const dateUpdated =
                vc.dateUpdated == null
                  ? null
                  : new Date(vc.dateUpdated).toLocaleDateString();
              data.push([vc.title, vc.numberOfVariants, dateUpdated]);
            });
            return data;
          },
        },
        template: `
          <div>
            <table ref="table" id="variant_configurations" class="display">
              <thead>
                  <tr>
                    <th>Name</th>
                    <th>Number of Variants</th>
                    <th>Last Updated</th>
                  </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        `,
      });

      Vue.component("VCNameStep", {
        props: {
          value: String,
        },
        template: `
          <div class="field width-100">
            <div class="heading">
              <label for="configuration-name">Name</label>
            </div>
            <div id="configuration-name-instructions" class="instructions">
              <p>Give your configuration a name e.g. "Accent Colours"</p>
            </div>
            <div class="input ltr">
              <textarea
                @input="$emit('input', $event.target.value)"
                id="configuration-name"
                class="nicetext text"
                name="title" rows="1"
                cols="50"
                placeholder=""
                style="min-height: 32px;"
              >{{value}}</textarea>
            </div>
          </div>
        `,
      });

      Vue.component("VCFieldsStep", {
        props: {
          vcFields: Array,
          value: Array,
        },
        data() {
          return {
            model: this.value,
          };
        },
        methods: {
          toggleSelectAll() {
            if (this.isAllSelected) {
              return (this.model = []);
            }

            this.model = [];
            this.vcFields.forEach((field) => {
              this.model.push(field.handle);
            });
          },
        },
        computed: {
          isAllSelected() {
            const handles = this.vcFields.map((field) => field.handle);
            const filteredArray = handles.filter((value) =>
              this.model.includes(value)
            );

            return filteredArray.length === handles.length;
          },
        },
        watch: {
          model: function (val) {
            this.$emit("input", val);
          },
        },
        template: `
          <div class="field width-100">
            <div class="heading">
              <label for="configuration-fields">Fields
                <a @click="toggleSelectAll" href="#" style="font-size: 11px; font-weight: normal; margin-left: 2px;">{{isAllSelected ? 'Unselect all' : 'Select all'}}</a>
              </label>
            </div>
            <div class="input ltr">
              <fieldset class="checkbox-group">
                <div v-for="field in vcFields">
                  <input
                    type="checkbox"
                    class="checkbox"
                    name="fields[]"
                    :id="'fields-'+field.handle"
                    :value="field.handle"
                    v-model="model"
                  >
                  <label :for="'fields-'+field.handle">
                    {{field.name}}
                  </label>
                </div>
              </fieldset>
            </div>
          </div>
        `,
      });

      Vue.component("VCValuesStep", {
        props: {
          vcFields: Array,
          value: Array,
        },
        data() {
          return {
            model: this.value,
          };
        },
        methods: {
          toggleSelectAll(handle) {
            const values = this.valuesByHandle[handle];
            if (!values.length) return;

            if (!this.isAllSelected(handle)) {
              this.model = [...this.model, ...values];
            } else {
              this.model = this.model.filter(
                (value) => values.indexOf(value) === -1
              );
            }
          },
          isAllSelected(handle) {
            const values = this.valuesByHandle[handle];

            const filteredArray = values.filter((value) =>
              this.model.includes(value)
            );

            return filteredArray.length === values.length;
          },
        },
        computed: {
          valuesByHandle() {
            const values = {};
            this.vcFields.forEach((field) => {
              values[field.handle] = field.values.map(({ value }) => value);
            });
            return values;
          },
        },
        watch: {
          model: function (val) {
            this.$emit("input", val);
          },
        },
        template: `
          <div>
            <div v-for="field in vcFields" class="field width-100">
              <div class="heading">
                <label for="configuration-fields">{{field.name}}
                  <a @click="toggleSelectAll(field.handle)" href="#" style="font-size: 11px; font-weight: normal; margin-left: 2px;">{{isAllSelected(field.handle) ? 'Unselect all' : 'Select all' }}</a>
                </label>
              </div>
              <div class="input ltr">
                <fieldset class="checkbox-group" style="display: flex; flex-wrap: wrap;">
                  <div v-for="{label, value} in field.values" style="width: 25%;">
                    <input type="checkbox" :id="'fields-'+value" class="checkbox" name="fields[]" :value="value" v-model="model">
                    <label :for="'fields-'+value">
                      {{label}}
                    </label>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>
        `,
      });

      Vue.component("VCFooter", {
        props: {
          isLoading: Boolean,
          priBtnText: String,
          secBtnText: String,
        },
        template: `
          <div class="footer">
            <div class="buttons right">
              <button @click="$emit('secBtnClick')" class="btn">{{secBtnText}}</button>
              <button
                @click="$emit('priBtnClick')"
                :disabled="isLoading"
                :class="{disabled: isLoading}"
                class="btn submit"
              >{{priBtnText}}</button>
            </div>
          </div>
        `,
      });

      Vue.component("VCLoading", {
        template: `
          <div style="display:flex; align-items: center;">
            Loading...
            <span class="spinner"></span>
          </div>
        `,
      });
    },

    /**
     * Fetches variant configurations from the API
     * @author Josh Smith <josh@batch.nz>
     * @return Promise
     */
    getVariantConfigurations(params = {}) {
      return $.ajax({
        method: "GET",
        url: Craft.CommerceUntitled.getApiUrl("variant-configurations", params),
      });
    },

    /**
     * Fetches variant configuration type fields from the API
     * @author Josh Smith <josh@batch.nz>
     * @return Promise
     */
    getVariantConfigurationTypeFields(params = {}) {
      return $.ajax({
        method: "GET",
        url: Craft.CommerceUntitled.getApiUrl(
          "variant-configuration-types/fields",
          params
        ),
      });
    },
  });
})(jQuery);
