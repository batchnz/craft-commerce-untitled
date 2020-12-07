<template>
  <div>
    <table ref="table" id="variant_configurations" class="display">
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
  mounted() {
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

    const self = this;
    this.$nextTick(() => {
      $(".dataTable").on("click", "tbody tr", function () {
        const data = self.dt.row(this).data();
        self.setCurrentVariantConfigurationById(data[0]);
      });
    });
  },
  methods: {
    ...mapActions(["setCurrentVariantConfigurationById"]),
  },
  beforeDestroy() {
    this.dt.destroy();
  },
};
</script>
