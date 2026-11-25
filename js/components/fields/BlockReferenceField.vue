<template>
  <k-field v-bind="$props" :input="id" class="k-select-field" @focusin="refresh">
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
      // page and language of the request in flight, to skip duplicate reloads
      loading: null
    };
  },
  computed: {
    // the sibling field "targetPage" of this block (field names are lower-cased)
    targetPage() {
      return this.formData?.targetpage ?? "";
    },
    // the content language, block content differs per language
    language() {
      return this.$panel.language?.code ?? "";
    },
    // what the block list depends on
    source() {
      return this.targetPage + "|" + this.language;
    }
  },
  watch: {
    source: {
      handler() {
        // never offer the blocks of the page or language selected before
        this.blockOptions = [];
        this.fillBlocks();
      },
      immediate: true
    }
  },
  methods: {
    async fillBlocks() {
      if (!this.targetPage) {
        return;
      }

      const source = this.source;

      if (this.loading === source) {
        return;
      }

      this.loading = source;
      let response;

      try {
        response = await this.$api.get("block-reference/blocks", {
          page: this.targetPage,
          language: this.language || undefined
        });
      } catch (error) {
        if (source === this.source) {
          this.$panel.error(error);
        }

        return;
      } finally {
        if (this.loading === source) {
          this.loading = null;
        }
      }

      // ignore the answer if another page or language was selected meanwhile
      if (source !== this.source) {
        return;
      }

      this.blockOptions = response.map((block, i) => {
        return {
          text: i + ": " + block.parent + " | " + block.type + " | " + this.preview(block),
          value: block.id
        };
      });
    },
    // the block content joined and cut to a short plain-text preview
    preview(block) {
      const formatted = block.formatted ?? [];

      return Object.entries(block.content)
        .map(([key, value]) => {
          const text = typeof value === "string" ? value : JSON.stringify(value);

          // only writer and list fields hold HTML, keep e.g. code as written
          return formatted.includes(key) ? text.replace(/<[^>]*>/g, " ") : text;
        })
        .join(" ~ ")
        .replace(/\s+/g, " ")
        .trim()
        .substring(0, 33);
    },
    // reload on every focus, so blocks saved since then (also on this page) show up
    refresh() {
      this.fillBlocks();
    }
  }
};
</script>
