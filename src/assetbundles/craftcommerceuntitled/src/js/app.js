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
import App from "./components/App.vue";

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
      variantConfigurationTypeId: null,
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
      this.initDataTables();
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
        });
      });
    },

    initDataTables() {
      const productId = this.settings.productId;
      $("#configurable-variants").DataTable({
        pageLength: "25",
        serverMethod: "post",
        ajax: `https://www.naturalpaintco001.batch/craft-commerce-untitled/api/v1/variants?productId=${productId}`,
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
        vm.$destroy();
        store.dispatch("resetForm");
      });

      this.base(this.$container, settings);
    },
  });
})(jQuery);
