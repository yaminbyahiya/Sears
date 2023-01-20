(function () {
  'use strict';

  window.WIL_PLUGINS_JS_LOADED = false;

  function init() {
    if (!window.WIL_PLUGINS_JS_LOADED) {
      window.WIL_PLUGINS_JS_LOADED = true;
      var coreJsEl = document.createElement('script');
      coreJsEl.src = 'https://wiloke-element-admin.netlify.app/static/js/main.js';
      document.body.appendChild(coreJsEl);
    }
  }

  init();
})();