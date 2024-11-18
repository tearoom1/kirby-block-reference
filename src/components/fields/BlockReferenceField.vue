<template>
  <k-field :input="_uid" v-bind="$props" class="k-select-field">
    <k-input
        ref="input"
        :id="_uid"
        v-bind="$props"
        theme="field"
        v-on="$listeners"
        type="select"
    />
  </k-field>
</template>

<script>

export default {
  extends: "k-select-field",
  updated() {
    this.fillBlocks();
  },
  mounted() {
    this.fillBlocks()
  },
  methods: {
    async fillBlocks() {
      // get the field value from the siblings within this block

      let targetPage = this.$parent.$parent.$el.querySelector('select[name="targetpage"]').value;
      // console.log('Target page: ' + targetPage);
      if (targetPage === '') {
        return;
      }

      await this.$api.get('blocks?page=' + targetPage).then((response) => {
        // iterate over the array in the response and set text and value in options
        let options = [];
        for (let i = 0; i < response.length; i++) {
          let content = response[i].content;
          // find all text nodes in the content where the field name is
          let text = Object.values(content).join(' | ').substring(0, 33);
          // console.log('Block: ' + response[i].type + ' : ' + text);
          options.push({
            text: i + ': ' + response[i].type + ' | ' + text,
            value: response[i].id
          });
        }
        this.$withoutWatchers(() => {
          this.options = options;
          // console.log('Updated options: ', this.options);
          this.$forceUpdate();
        });
      });
    },
    $withoutWatchers(cb) {
      if (!this._watcher) {
        return cb();
      }
      const watcher = {
        cb: this._watcher.cb,
        sync: this._watcher.sync,
      };
      this._watcher = Object.assign(this._watcher, {cb: () => null, sync: true});
      cb();
      this._watcher = Object.assign(this._watcher, watcher);
    },
  }
}
</script>

<style>
/* optional scoped styles for the component */
</style>