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

import Vue from "vue";
import store from "./store";
import Api from "./api";
import App from "./components/App.vue";

import "../css/app.scss";

(function ($) {
  // Set datatables classes
  $.fn.dataTable.ext.classes.sPageButton = `${$.fn.dataTable.ext.classes.sPageButton} btn`;
  $.fn.dataTable.ext.classes.sPageButtonActive = `active`;
  $.fn.dataTable.ext.classes.sFilterInput = "text dataTables_filter__input";
  $.fn.dataTable.ext.classes.sSortAsc = "ordered asc";
  $.fn.dataTable.ext.classes.sSortColumn = "ordered";
  $.fn.dataTable.ext.classes.sSortDesc = "ordered desc";

  /**
   * The main plugin object
   * Responsible for setting up the variant configuration interactivity
   * @author Josh Smith <hey@joshthe.dev>
   */
  Craft.CommerceUntitled = Garnish.Base.extend({
    settings: {
      productId: null,
      productTypeId: null,
      variantConfigurationTypeId: null,
      productVariantType: "standard",
    },
    $dt: $(),

    /**
     * Initialises this object
     * @author Josh Smith <hey@joshthe.dev>
     * @param  object settings
     * @return void
     */
    init(settings) {
      const self = this;
      this.settings = $.extend({}, Craft.CommerceUntitled.defaults, settings);

      // Setup the configurable product
      if (this.settings.productVariantType !== "standard") {
        this.initEditBtn();
        this.initDataTables();
      }

      // Handle the product variant type select change event
      this.handleVariantTypeChange();
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
          variantConfigurationTypeId: this.settings.variantConfigurationTypeId,
          // Destroy the Vue instance and reload datatables data
          onClose: (vm) => {
            vm.$destroy();
            store.dispatch("resetForm");
            this.$dt.ajax.reload();
          },
        });
      });
    },

    /**
     * Intialises the main variants data table
     * @author Josh Smith <hey@joshthe.dev>
     * @return void
     */
    initDataTables() {
      const productId = this.settings.productId;
      this.$dt = $("#configurable-variants").DataTable({
        autoWidth: false,
        ajax: `/craft-commerce-untitled/api/v1/variants?productId=${productId}`,
      });
    },

    /**
     * Handles the change of the variant type select menu
     * @author Josh Smith <josh@batch.nz>
     * @return void
     */
    handleVariantTypeChange() {
      const productId = this.settings.productId;
      $("#variant-type-field")
        .find("select")
        .on("change", (e) => {
          const variantType = e.target.value;
          Api.saveProductVariantType(productId, { variantType }).then(() => {
            window.location.reload();
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
   * Variant Configuration Modal Object
   * @type Garnish.Modal
   */
  Craft.VariantConfigurationModal = Garnish.Modal.extend({
    settings: {
      productId: null,
      productTypeId: null,
      variantConfigurationTypeId: null,
      onClose: () => {},
    },
    closeOtherModals: true,
    shadeClass: "modal-shade dark",

    init(settings) {
      const self = this;

      this.setSettings(settings, Craft.VariantConfigurationModal.defaults);

      // Append the root element
      this.$container = $(
        `<div class="modal elementselectormodal">
          <div id="variant-configuration-modal-container" style="height: 100%; overflow-y: auto; overscroll-behavior: contain;">
            <div id="variant-configuration-app" />
          </div>
        </div>`
      ).appendTo(Garnish.$bod);

      const vm = new Vue({
        el: "#variant-configuration-app",
        store,
        render: (h) =>
          h(App, {
            props: {
              productId: this.settings.productId,
              productTypeId: this.settings.productTypeId,
              variantConfigurationTypeId: this.settings
                .variantConfigurationTypeId,
              $modal: this,
            },
          }),
      });

      // Destroy the Vue instance when hidden
      this.on("hide", () => {
        this.settings.onClose(vm);
      });

      this.base(this.$container, settings);
    },
  });
})(jQuery);
