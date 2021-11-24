<template>
  <div>
    <table ref="table" id="variant_configurations" class="display data">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Number of Variants</th>
          <th>Last Updated</th>
          <th></th>
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
        data.push({
          id: config.id,
          name: config.title,
          num: config.numberOfVariants,
          date: dateUpdated,
        });
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
        columns:[
          {data: "id"},
          {data: "name"},
          {data: "num"},
          {data: "date"},
          {
            name: "control",
            searchable: false,
            title: "",
            orderable: false,
            defaultContent: "<input type=\"button\" value=\"Click me\">",
            createdCell: function(cell, cellData, rowData, rowIndex, colIndex) {
              $(cell).on("click", "input", function on_GridActionButton_Click(event) {
              alert("You clicked in " + event.data.name + "'s row");
            });
            }
          }
        ]
      });

      // Add a click event handler on the datatable
      $(".dataTable").on("click", "tbody tr", function () {
        const data = self.dt.row(this).data();
        self.clearFormErrors();
        self.setCurrentVariantConfigurationById(data.id);
      });
    });
  },
  methods: {
    ...mapActions([
      "setCurrentVariantConfigurationById",
      "createNewVariantConfiguration",
      "clearFormErrors",
      "deleteVariantConfiguration"
    ]),
  },
  beforeDestroy() {
    this.dt.destroy();
    AsyncEventBus.off("form-submission", this.createNewVariantConfiguration);
  },
};
</script>
