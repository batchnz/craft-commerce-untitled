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
import { mapActions, mapState, mapMutations, mapGetters } from "vuex";
import AsyncEventBus from "../../store/asyncEventBus";
import * as MUTATIONS from "../../constants/mutationTypes";

export default {
  data() {
    return {
      dt: null,
    };
  },
  computed: {
    ...mapState({
      productId: (state) => state.productId,
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
  },
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
            defaultContent: "<input type=\"button\" class=\"delete btn\" value=\"Delete\">",
            createdCell: function(cell, cellData, rowData, rowIndex, colIndex) {
              $(cell).on(
                "click",
                "input", 
                async () => {
                  if (!confirm(`Are you sure you want to permanently delete the ${rowData.name} variant configuration?`)) return;

                  self.setIsLoading(true);
                  try {
                    const result = await self.deleteVariantConfiguration(rowData.id);
                    await self.fetchVariantConfigurations({
                      "productId": self.productId,
                    });
                  } catch (e) {
                    console.error(e);
                  } finally {
                    self.resetForm();
                    self.setIsLoading(false);
                  }
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
      "resetForm",
      "fetchVariantConfigurations",
      "fetchVariantConfigurationTypeFields",
    ]),
    ...mapMutations({
      setIsLoading: MUTATIONS.SET_IS_LOADING,
    }),

  },
  beforeDestroy() {
    this.dt.destroy();
    AsyncEventBus.off("form-submission", this.createNewVariantConfiguration);
  },
};
</script>
