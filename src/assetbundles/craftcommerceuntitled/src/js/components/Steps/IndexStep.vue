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
          {
            data: "id",
            createdCell: function(cell, cellData, rowData, rowIndex, colIndex) {
                $(cell).addClass("variant-cell");
            }
          },
          {
            data: "name",
            createdCell: function(cell, cellData, rowData, rowIndex, colIndex) {
                $(cell).addClass("variant-cell");
            }
          },
          {
            data: "num",
            createdCell: function(cell, cellData, rowData, rowIndex, colIndex) {
                $(cell).addClass("variant-cell");
            }
          },
          {
            data: "date",
            createdCell: function(cell, cellData, rowData, rowIndex, colIndex) {
                $(cell).addClass("variant-cell");
            }
          },
          {
            name: "control",
            searchable: false,
            title: "",
            orderable: false,
            defaultContent: "<input type=\"button\" class=\"delete\" value=\"Click me\">",
            createdCell: function(cell, cellData, rowData, rowIndex, colIndex) {
              $(cell).on(
                "click",
                "input", 
                async () => {
                  const result = await self.deleteVariantConfiguration(rowData.id);
                  const json = await result.json();
                  console.log(json);
                  self.resetForm();
                }
              );
            }
          }
        ]
      });

      // Add a click event handler on the datatable
      $(".dataTable").on("click", ".variant-cell", function () {
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
      "deleteVariantConfiguration",
      "resetForm"
    ]),
  },
  beforeDestroy() {
    this.dt.destroy();
    AsyncEventBus.off("form-submission", this.createNewVariantConfiguration);
  },
};
</script>
