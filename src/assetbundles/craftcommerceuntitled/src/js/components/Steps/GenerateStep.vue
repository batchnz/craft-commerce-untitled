<template>
  <div v-if="!isGenerating">
    <h2 style="margin-bottom: 5px">Config Summary</h2>
    <div style="margin-bottom: 16px">The following config will be saved:</div>
    <div class="field" style="margin-bottom: 16px 0">
      <div class="heading">
        <label>Title</label>
      </div>
      <div style="margin-bottom: 16px">{{ variantConfiguration.title }}</div>

      <div v-for="field in selectedFields">
        <div style="margin-bottom: 5px; font-weight: bold; color: #606d7b">
          {{ field.name }}
        </div>
        <ul style="margin: 0 0 16px 16px; list-style-type: disc">
          <li
            v-for="{ label, value } in selectedOptionsByFieldHandle[
              field.handle
            ]"
          >
            {{ label }}
          </li>
        </ul>
      </div>
      <div class="heading">
        <label>Settings</label>
      </div>
      <ul
        v-for="type in types"
        style="margin: 0 0 0 16px; list-style-type: disc"
      >
        <li v-if="variantConfiguration.settings[type].method === 'all'">
          {{ getTitle(type) }} - {{ type === "price" ? "$" : ""
          }}{{ variantConfiguration.settings[type].values.value }}
          for all variants
        </li>
        <template v-if="variantConfiguration.settings[type].method === 'field'">
          <li>{{ getTitle(type) }} - Per field</li>
          <ul style="margin-left: 16px; list-style-type: revert">
            <li
              v-for="(price, fieldId) in variantConfiguration.settings[type]
                .values"
              v-if="fieldId !== 'value'"
            >
              {{ optionValuesById[fieldId] }} - {{ type === "price" ? "$" : ""
              }}{{ price }}
            </li>
          </ul>
        </template>
        <li v-if="variantConfiguration.settings[type].method === 'skip'">
          {{ getTitle(type) }} - Skipped
        </li>
      </ul>
    </div>

    <!-- <p><strong>3</strong> variants will be removed.</p> -->
  </div>
  <div v-else>
    <h2>Hold Tight!</h2>
    <p>
      Please wait while your variants are generated.<br />
      This can take several minutes and you can monitor progress on the queue
      jobs page.
    </p>
  </div>
</template>

<script>
import { mapActions, mapMutations, mapState, mapGetters } from "vuex";
import { TYPES } from "../../constants/settingsTypes";
import { SET_IS_COMPLETED } from "../../constants/mutationTypes";
import AsyncEventBus from "../../store/asyncEventBus";

export default {
  data() {
    return {
      isGenerating: false,
    };
  },
  computed: {
    ...mapState({
      variantConfiguration: (state) => state.variantConfiguration,
    }),
    ...mapGetters([
      "selectedFields",
      "selectedOptionsByFieldHandle",
      "fieldValuesByHandle",
      "optionValuesById",
    ]),
    types() {
      return TYPES;
    },
  },
  created() {
    AsyncEventBus.once("form-submission", this, this.handleGenerateVariants);
  },
  beforeDestroy() {
    AsyncEventBus.off("form-submission", this.handleGenerateVariants);
  },
  methods: {
    ...mapActions(["generateVariants"]),
    ...mapMutations({ setIsCompleted: SET_IS_COMPLETED }),
    getTitle(title = "") {
      return title.charAt(0).toUpperCase() + title.slice(1);
    },
    async handleGenerateVariants() {
      await this.generateVariants(this.variantConfiguration.id);
      this.setIsCompleted();
      this.isGenerating = true;
    },
  },
};
</script>
