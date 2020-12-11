<template>
  <div>
    <table ref="table" id="variant_configurations" class="display data">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Number of Variants</th>
          <th>Last Updated</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</template>

<script>
import { mapActions, mapState } from "vuex";
import AsyncEventBus from "../../store/asyncEventBus";

export default {
  data() {
    return {
      dt: null,
    };
  },
  computed: mapState({
    variantConfigurations: (state) => state.variantConfigurations,
    dtData() {
      const data = [];
      this.variantConfigurations.forEach((config) => {
        const dateUpdated =
          config.dateUpdated == null
            ? null
            : new Date(config.dateUpdated).toLocaleDateString();
        data.push([
          config.id,
          config.title,
          config.numberOfVariants,
          dateUpdated,
        ]);
      });
      return data;
    },
  }),
  created() {
    AsyncEventBus.once("form-submission", this.createNewVariantConfiguration);
  },
  mounted() {
    const self = this;

    this.$nextTick(() => {
      // Initialise the datatable
      this.dt = $(this.$refs.table).DataTable({
        data: this.dtData,
        columnDefs: [
          {
            targets: [0],
            visible: false,
            searchable: false,
          },
        ],
      });

      // Add a click event handler on the datatable
      $(".dataTable").on("click", "tbody tr", function () {
        const data = self.dt.row(this).data();
        self.clearFormErrors();
        self.setCurrentVariantConfigurationById(data[0]);
      });
    });
  },
  methods: {
    ...mapActions([
      "setCurrentVariantConfigurationById",
      "createNewVariantConfiguration",
      "clearFormErrors",
    ]),
  },
  beforeDestroy() {
    this.dt.destroy();
    AsyncEventBus.off("form-submission", this.createNewVariantConfiguration);
  },
};
</script>
