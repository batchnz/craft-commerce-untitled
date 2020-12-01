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
    data: {},
    closeOtherModals: true,
    shadeClass: "modal-shade dark",
    isLoading: true,
    variantConfigurations: [],
    variantConfigurationTypeFields: [],
    $table: $(),
    $form: $(),
    $body: $(),
    $footer: $(),

    init(settings) {
      const self = this;
      this.settings = $.extend(
        {},
        Craft.VariantConfigurationModal.defaults,
        settings
      );

      this.$form = $(
        `<form class="modal elementselectormodal" method="post" accept-charset="UTF-8">
          ${Craft.getCsrfInput()}
        </form>`
      ).appendTo(Garnish.$bod);

      // Append base nodes to the form
      this.$body = $("<div />").appendTo(this.$form);
      this.$footer = $("<div />").appendTo(this.$form);

      // Render the index step
      this.goToStep("index");

      this.base(this.$form, settings);
    },

    /**
     * Handles the form step change
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    handleFormStep(e) {
      const {
        data: { step },
      } = e;
      return this.goToStep(step);
    },

    /**
     * Renders the form step
     * @author Josh Smith <josh@batch.nz>
     * @param  int step
     * @return void
     */
    goToStep(step = null) {
      switch (step) {
        case 1:
          return this.renderNameStep();
        case 2:
          return this.renderFieldsStep();
        case 3:
          return this.renderValuesStep();
        case 4:
          return this.renderSettingsStep();
        case "index":
          return this.renderIndex();
        default:
          return this.hide();
      }
    },

    /**
     * Renders the index step
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    renderIndex() {
      this.updateBody();
      this.updateFooter();
      this.initVCTable();
    },

    /**
     * Renders the configuration name caputure step
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    renderNameStep() {
      const content = `
        <div class="field width-100">
          <div class="heading">
            <label for="configuration-name">Name</label>
          </div>
          <div id="configuration-name-instructions" class="instructions">
            <p>Give your configuration a name e.g. "Accent Colours"</p>
          </div>
          <div class="input ltr">
            <textarea id="configuration-name" class="nicetext text" name="name" rows="1" cols="50" placeholder="" style="min-height: 32px;"></textarea>
          </div>
        </div>
      `;

      this.updateBody({
        subTitle: "Step 1. Set the configuration name",
        content,
      });
      this.updateFooter("Next", 2, "Previous", "index");

      // Update the title property on keyup
      this.$body.find("#configuration-name").on("keyup", (e) => {
        this.data.title = e.target.value;
      });
    },

    /**
     * Renders the fields selection form step
     * Allows the user to pick a selection of fields to generate variants from
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    renderFieldsStep() {
      let $fieldsLoadPromise = new $.Deferred();
      const subTitle = "Step 2. Select the fields to generate variants from";

      this.updateBody({
        subTitle,
        content: this.getLoadingElement()[0].outerHTML,
      });

      this.updateFooter("Next", 3, "Previous", 1);

      this.setModalLoading();

      // Fetch variant configuration fields
      if (!this.variantConfigurationTypeFields.length) {
        $fieldsLoadPromise = this.getVariantConfigurationTypeFields({
          productTypeId: this.settings.productTypeId,
        }).done((res) => {
          this.variantConfigurationTypeFields = res.data;
        });
      } else {
        $fieldsLoadPromise.resolve();
      }

      // Render the options
      $fieldsLoadPromise
        .done(() => {
          const $content = $(`
          <div class="field width-100">
            <div class="heading">
              <label for="configuration-fields">Fields
                <a href="#" style="font-size: 11px; font-weight: normal; margin-left: 2px;">Select all</a>
              </label>
            </div>
            <div class="input ltr">
              <fieldset class="checkbox-group">
                ${this.variantConfigurationTypeFields
                  .map(
                    (field) => `
                      <div>
                        <input type="checkbox" id="fields-${field.handle}" class="checkbox js--fields-checkbox" name="fields[]" value="${field.handle}">
                        <label for="fields-${field.handle}">
                          ${field.name}
                        </label>
                      </div>`
                  )
                  .join("")}
              </fieldset>
            </div>
          </div>
        `);

          this.updateBody({
            subTitle,
            content: $content[0].outerHTML,
          });

          this.$body.find(".js--fields-checkbox").on("change", (e) => {
            if (!this.data.fields) {
              this.data.fields = new Set();
            }

            if (e.target.checked) {
              this.data.fields.add(e.target.value);
            } else {
              this.data.fields.delete(e.target.value);
            }
            console.log("this.data.fields:", this.data.fields);
          });
        })
        .always(() => {
          this.setModalLoading(false);
        });
    },

    /**
     * Renders the field values selection form step
     * Allows the user to pick a selection of field values to generate variants from
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    renderValuesStep() {
      let $fieldsLoadPromise = new $.Deferred();
      const subTitle = "Step 3. Select the values to generate variants from";

      this.updateBody({
        subTitle,
        content: this.getLoadingElement()[0].outerHTML,
      });

      this.setModalLoading();

      // Fetch variant configuration fields
      if (!this.variantConfigurationTypeFields.length) {
        $fieldsLoadPromise = this.getVariantConfigurationTypeFields({
          productTypeId: this.settings.productTypeId,
        }).done((res) => {
          this.variantConfigurationTypeFields = res.data;
        });
      } else {
        $fieldsLoadPromise.resolve();
      }

      // Render the options
      $fieldsLoadPromise
        .done(() => {
          const $content = $("<div />");

          this.variantConfigurationTypeFields.forEach((field) => {
            $content.append(`
            <div class="field width-100">
              <div class="heading">
                <label for="configuration-fields">${field.name}
                  <a href="#" style="font-size: 11px; font-weight: normal; margin-left: 2px;">Select all</a>
                </label>
              </div>
              <div class="input ltr">
                <fieldset class="checkbox-group" style="display: flex; flex-wrap: wrap;">
                  ${field.values
                    .map(
                      ({ label, value }) => `<div style="width: 25%;">
                        <input type="checkbox" id="fields-${value}" class="checkbox" name="fields[]" value="${value}">
                        <label for="fields-${value}">
                          ${label}
                        </label>
                      </div>`
                    )
                    .join("")}
                </fieldset>
              </div>
            </div>
          `);
          });

          this.updateBody({
            subTitle,
            content: $content.html(),
          });
        })
        .always(() => {
          this.setModalLoading(false);
        });

      this.updateFooter("Next", 4, "Previous", 2);
    },

    /**
     * Renders the settings selection form step
     * Allows the user to set configuration settings
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    renderSettingsStep() {},

    /**
     * Helper function to update the modal body
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    updateBody({ title, subTitle, content } = {}) {
      return this.$body.html(this.getBodyElement({ title, subTitle, content }));
    },

    /**
     * Helper function to update the modal footer
     * @author Josh Smith <josh@batch.nz>
     * @param  string btnPriText
     * @param  int    btnPriStep
     * @param  string btnSecText
     * @param  int    btnSecStep
     * @return void
     */
    updateFooter(btnPriText, btnPriStep = 1, btnSecText, btnSecStep) {
      this.$footer.html(
        this.getFooterElement({
          btnPriText,
          btnSecText,
          cb: ({ $btnPri, $btnSec }) => {
            this.addListener(
              $btnPri,
              "click",
              { step: btnPriStep },
              "handleFormStep"
            );
            this.addListener(
              $btnSec,
              "click",
              { step: btnSecStep },
              "handleFormStep"
            );
          },
        })
      );
    },

    /**
     * Method to update the modal loading state
     * @author Josh Smith <josh@batch.nz>
     * @param  {Boolean} loading
     */
    setModalLoading(loading = true) {
      if (loading) {
        this.$footer
          .find(".btn.submit")
          .attr("disabled", "disabled")
          .addClass("disabled");
      } else {
        this.$footer
          .find(".btn.submit")
          .removeAttr("disabled")
          .removeClass("disabled");
      }
    },

    /**
     * Initialises the variant configurations datatable
     * @author Josh Smith <josh@batch.nz>
     * @return Promise
     */
    initVCTable() {
      let $loadVCPromise = new $.Deferred();

      this.$body.find(".js--content").append(`
        <div class="js--datatable">
          ${this.getLoadingElement()[0].outerHTML}
        </div>
      `);

      this.setModalLoading();

      // Load the variant configurations
      if (this.variantConfigurations.length) {
        $loadVCPromise.resolve();
      } else {
        $loadVCPromise = this.getVariantConfigurations({
          productId: this.settings.productId,
        }).done((configurations) => {
          this.variantConfigurations = configurations.data;
        });
      }

      $loadVCPromise
        .done(() => {
          const $content = this.$form.find(".js--content");
          $content.html("");

          // Create the table element
          this.$table = this.getTableElement().appendTo($content);

          // Assemble table data
          const data = [];
          this.variantConfigurations.forEach((vc) => {
            const dateUpdated =
              vc.dateUpdated == null
                ? null
                : new Date(vc.dateUpdated).toLocaleDateString();
            data.push([vc.title, vc.numberOfVariants, dateUpdated]);
          });

          // Init the DataTable
          this.$table.DataTable({ data });

          return $loadVCPromise;
        })
        .always(() => {
          this.setModalLoading(false);
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

    /**
     * Returns the Body HTML
     * @author Josh Smith <josh@batch.nz>
     * @param  {String} options.title
     * @param  {String} options.subTitle
     * @return {object}
     */
    getBodyElement({ title, subTitle, content } = {}) {
      return $(`
        <div class="body">
          <div class="content-summary">
            ${this.getHeadingElement({ title, subTitle })[0].outerHTML}
          </div>
          <div class="js--content">
            ${content || ``}
          </div>
        </div>
      `);
    },

    /**
     * Returns the footer element
     * @author Josh Smith <josh@batch.nz>
     * @param  {function} cb
     * @return {object}
     */
    getFooterElement({ btnSecText, btnPriText, cb = () => {} } = {}) {
      // Footer + Buttons
      const $footer = $(`<div class="footer" />`);

      const $btnContainer = $('<div class="buttons right fab-my-0"/>').appendTo(
        $footer
      );

      const $btnSec = $(
        '<div class="btn">' + Craft.t("app", btnSecText || "Cancel") + "</div>"
      ).appendTo($btnContainer);

      const $btnPri = $(
        '<div class="btn submit">' +
          Craft.t("app", btnPriText || "Create New") +
          "</div>"
      ).appendTo($btnContainer);

      // Run a callback function to give an opportunity to assign event handlers to the buttons
      if (typeof cb === "function") {
        cb({ $footer, $btnContainer, $btnSec, $btnPri });
      } else {
        // Hide the modal when the cancel button is clicked
        this.addListener($btnSec, "click", "hide");
      }

      return $footer;
    },

    /**
     * Returns the modal heading HTML
     * @author Josh Smith <josh@batch.nz>
     * @param  {String} options.title
     * @param  {String} options.subTitle
     * @return {object}
     */
    getHeadingElement({
      title = "Variant Configurator",
      subTitle = "Select or create a variant configuration",
    } = {}) {
      return $(`<div>
        <h1 style="font-size: 20px;font-weight: bold;">
          ${title}
        </h1>
        <p style="margin-bottom: 2rem !important;">
          ${subTitle}
        </p>
        </div>
      `);
    },

    /**
     * Returns the base data tables HTML
     * @author Josh Smith <josh@batch.nz>
     * @return {object}
     */
    getTableElement() {
      return $(`
        <table id="variant_configurations" class="display">
          <thead>
              <tr>
                  <th>Name</th>
                  <th>Number of Variants</th>
                  <th>Last Updated</th>
              </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      `);
    },

    /**
     * Returns the loading element
     * @author Josh Smith <josh@batch.nz>
     * @return object
     */
    getLoadingElement({ loadingText = "Loading" } = {}) {
      return $(`
        <div style="display:flex; align-items: center;">
          ${loadingText}...
          <span class="spinner"></span>
        </div>
      `);
    },
  });
})(jQuery);
