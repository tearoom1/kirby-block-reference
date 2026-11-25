<template>
  <k-field v-bind="$props" :input="id" class="k-select-field" @focusin="retry">
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
      blockOptions: [],
      // the page whose blocks could not be loaded, tried again on focus
      failedPage: null
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
      // never offer the blocks of the page selected before
      this.blockOptions = [];
      this.failedPage = null;

      if (!this.targetPage) {
        return;
      }

      const targetPage = this.targetPage;
      let response;

      try {
        response = await this.$api.get("block-reference/blocks", {page: targetPage});
      } catch (error) {
        if (targetPage === this.targetPage) {
          this.failedPage = targetPage;
          this.$panel.error(error);
        }

        return;
      }

      // ignore the answer if another page was selected meanwhile
      if (targetPage !== this.targetPage) {
        return;
      }

      this.blockOptions = response.map((block, i) => {
        // join the block content and cut it to a short preview, without the
        // HTML tags of writer and text blocks
        const text = Object.values(block.content)
          .join(" ~ ")
          .replace(/<[^>]*>/g, " ")
          .replace(/\s+/g, " ")
          .trim()
          .substring(0, 33);

        return {
          text: i + ": " + block.parent + " | " + block.type + " | " + text,
          value: block.id
        };
      });
    },
    retry() {
      if (this.failedPage && this.failedPage === this.targetPage) {
        this.fillBlocks();
      }
    }
  }
};
</script>
