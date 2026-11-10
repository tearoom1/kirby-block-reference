<template>
  <k-field v-bind="$props" :input="id" class="k-select-field">
    <k-input
        ref="input"
        v-bind="$props"
        :id="id"
        :options="blockOptions"
        theme="field"
        type="select"
        @input="$emit('input', $event)"
    />
  </k-field>
</template>

<script>

export default {
  extends: "k-select-field",
  props: {
    // all values of the surrounding form, sent by the Panel to every field
    formData: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      blockOptions: []
    };
  },
  computed: {
    // the sibling field "targetPage" of this block (field names are lower-cased)
    targetPage() {
      return this.formData?.targetpage ?? "";
    }
  },
  watch: {
    targetPage: {
      handler() {
        this.fillBlocks();
      },
      immediate: true
    }
  },
  methods: {
    async fillBlocks() {
      if (!this.targetPage) {
        this.blockOptions = [];
        return;
      }

      const targetPage = this.targetPage;
      const response = await this.$api.get("block-reference/blocks", {page: targetPage});

      // ignore the answer if another page was selected meanwhile
      if (targetPage !== this.targetPage) {
        return;
      }

      this.blockOptions = response.map((block, i) => {
        // join the block content and cut it to a short preview
        const text = Object.values(block.content).join(" ~ ").substring(0, 33);

        return {
          text: i + ": " + block.parent + " | " + block.type + " | " + text,
          value: block.id
        };
      });
    }
  }
};
</script>
