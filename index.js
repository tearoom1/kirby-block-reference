(function() {
  "use strict";
  function normalizeComponent(scriptExports, render, staticRenderFns, functionalTemplate, injectStyles, scopeId, moduleIdentifier, shadowMode) {
    var options = typeof scriptExports === "function" ? scriptExports.options : scriptExports;
    if (render) {
      options.render = render;
      options.staticRenderFns = staticRenderFns;
      options._compiled = true;
    }
    return {
      exports: scriptExports,
      options
    };
  }
  const _sfc_main = {
    extends: "k-select-field",
    updated() {
      this.fillBlocks();
    },
    mounted() {
      this.fillBlocks();
    },
    methods: {
      async fillBlocks() {
        let targetPage = this.$parent.$parent.$el.querySelector('select[name="targetpage"]').value;
        if (targetPage === "") {
          return;
        }
        await this.$api.get("blocks?page=" + targetPage).then((response) => {
          let options = [];
          for (let i = 0; i < response.length; i++) {
            let content = response[i].content;
            let text = Object.values(content).join(" | ").substring(0, 33);
            options.push({
              text: i + ": " + response[i].parent + " | " + response[i].type + " | " + text,
              value: response[i].id
            });
          }
          this.$withoutWatchers(() => {
            this.options = options;
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
          sync: this._watcher.sync
        };
        this._watcher = Object.assign(this._watcher, { cb: () => null, sync: true });
        cb();
        this._watcher = Object.assign(this._watcher, watcher);
      }
    }
  };
  var _sfc_render = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("k-field", _vm._b({ staticClass: "k-select-field", attrs: { "input": _vm._uid } }, "k-field", _vm.$props, false), [_c("k-input", _vm._g(_vm._b({ ref: "input", attrs: { "id": _vm._uid, "theme": "field", "type": "select" } }, "k-input", _vm.$props, false), _vm.$listeners))], 1);
  };
  var _sfc_staticRenderFns = [];
  _sfc_render._withStripped = true;
  var __component__ = /* @__PURE__ */ normalizeComponent(
    _sfc_main,
    _sfc_render,
    _sfc_staticRenderFns
  );
  __component__.options.__file = "/Users/mathis/Work/Clients/Rasmus Bielefeld/erasmus-bielefeld.de/site/plugins/kirby-block-reference/src/components/fields/BlockReferenceField.vue";
  const BlockReferenceField = __component__.exports;
  panel.plugin("tearoom1/block-reference", {
    fields: {
      blockReference: BlockReferenceField
    }
  });
})();
