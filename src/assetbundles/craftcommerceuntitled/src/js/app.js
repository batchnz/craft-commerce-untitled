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

      // Append the root element
      this.$container = $(
        `<div class="modal elementselectormodal">
          <div id="variant-configuration-app" />
        </div>`
      ).appendTo(Garnish.$bod);

      new Vue({
        el: "#variant-configuration-app",
        render: (h) =>
          h(App, {
            props: {
              productId: this.settings.productId,
              productTypeId: this.settings.productTypeId,
            },
          }),
      });

      this.base(this.$container, settings);
    },
  });
})(jQuery);
