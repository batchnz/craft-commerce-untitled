<template>
  <div>
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
          {{ getTitle(type) }} - ${{
            variantConfiguration.settings[type].values.value
          }}
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
              {{ optionValuesById[fieldId] }} - ${{ price }}
            </li>
          </ul>
        </template>
        <li v-if="variantConfiguration.settings[type].method === 'skip'">
          {{ getTitle(type) }} - Skipped
        </li>
      </ul>
    </div>

    <p><strong>3</strong> variants will be removed.</p>
  </div>
</template>

<script>
import { mapState, mapGetters } from "vuex";
import { TYPES } from "../../constants/settingsTypes";

export default {
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
  methods: {
    getTitle(title = "") {
      return title.charAt(0).toUpperCase() + title.slice(1);
    },
  },
};
</script>
