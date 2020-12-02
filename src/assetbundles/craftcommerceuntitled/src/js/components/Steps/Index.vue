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
export default {
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
    configs: {
      type: Array,
      default() {
        return [];
      },
    },
  },
  computed: {
    dtData() {
      const data = [];
      this.configs.forEach((vc) => {
        const dateUpdated =
          vc.dateUpdated == null
            ? null
            : new Date(vc.dateUpdated).toLocaleDateString();
        data.push([vc.title, vc.numberOfVariants, dateUpdated]);
      });
      return data;
    },
  },
};
</script>
