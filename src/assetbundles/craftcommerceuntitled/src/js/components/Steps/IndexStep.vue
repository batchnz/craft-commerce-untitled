<template>
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
</template>

<script>
import { mapState } from "vuex";

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
        data.push([config.title, config.numberOfVariants, dateUpdated]);
      });
      return data;
    },
  }),
  mounted() {
    this.dt = $(this.$refs.table).DataTable({ data: this.dtData });
  },
  beforeDestroy() {
    this.dt.destroy();
  },
};
</script>
