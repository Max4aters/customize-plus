/**
 * Utils
 *
 * @class api.Utils
 */
var Utils = (function () {

  /** @type {String} */
  var _IMAGES_BASE_URL = api.constants['IMAGES_BASE_URL'];

  /** @type {String} */
  var _DOCS_BASE_URL = api.constants['DOCS_BASE_URL'];

  /** @type {String} */
  var _SETTINGS_PREFIX = api.constants['SETTINGS_PREFIX'];

  /** @type {RegExp} */
  var regexSettingApi = new RegExp(_SETTINGS_PREFIX + '\\[.*\\]');

  /**
   * Is it an absolute URL?
   *
   * {@link http://stackoverflow.com/a/19709846/1938970}
   * @param  {String}  url The URL to test
   * @return {Boolean}     Whether is absolute or relative
   */
  function _isAbsoluteUrl (url) {
    // @@todo move to Regexes modules and create tests \\
    var r = new RegExp('^(?:[a-z]+:)?//', 'i');
    return r.test(url);
  }

  /**
   * Clean URL from multiple slashes
   *
   * Strips possible multiple slashes caused by the string concatenation or dev errors
   *
   * @param  {String} url
   * @return {String}
   */
  function _cleanUrlFromMultipleSlashes (url) {
    // @@todo move to Regexes modules and create tests \\
    return url.replace(/[a-z-A-Z-0-9_]{1}(\/\/+)/g, '/');
  }

  /**
   * Get a clean URL
   *
   * If an absolute URL is passed we just strip multiple slashes,
   * if a relative URL is passed we also prepend the right base url.
   *
   * @param  {String} url
   * @param  {String} type
   * @return {String}
   */
  function _getCleanUrl (url, type) {
    // return the absolute url
    var finalUrl = url;
    if (!_isAbsoluteUrl(url)) {
      switch (type) {
        case 'img':
          finalUrl = _IMAGES_BASE_URL + url;
          break;
        case 'docs':
          finalUrl = _DOCS_BASE_URL + url;
          break;
        default:
          break;
      }
    }
    return _cleanUrlFromMultipleSlashes(finalUrl);
  }

  // @access public
  return {
    /**
     * To Boolean
     * '0' or '1' to boolean
     *
     * @method
     * @param  {strin|number} value
     * @return {boolean}
     */
    _toBoolean: function (value) {
      return typeof value === 'boolean' ? value : !!parseInt(value, 10);
    },
    /**
     * Strip HTML from input
     * {@link http://stackoverflow.com/q/5002111/1938970}
     * @param  {string} input
     * @return {string}
     */
    stripHTML: function (input) {
      return $(document.createElement('div')).html(input).text();
    },
    /**
     * Get image url
     *
     * @param  {String} url The image URL, relative or absolute
     * @return {String}     The absolute URL of the image
     */
    getImageUrl: function (url) {
      return _getCleanUrl(url, 'img');
    },
    /**
     * Get docs url
     *
     * @param  {String} url The docs URL, relative or absolute
     * @return {String}     The absolute URL of the docs
     */
    getDocsUrl: function (url) {
      return _getCleanUrl(url, 'docs');
    },
    /**
     * Bind a link element or directly link to a specific control to focus
     *
     * @method
     */
    linkControl: function (linkEl, controlId) {
      var controlToFocus = wpApi.control(controlId);
      var innerLink = function () {
        try {
          // try this so it become possible to use this function even
          // with WordPress native controls which don't have this method
          controlToFocus.inflate(true);

          // always deactivate search, it could be that we click on this
          // link from a search result try/catch because search is not
          // always enabled
          api.components.Search.deactivate();
        } catch(e) {
          console.warn('Utils->linkControl: failed attempt to deactivate Search', e);
        }

        controlToFocus.focus();
        controlToFocus.container.addClass('pwpcp-control-focused');
        setTimeout(function () {
          controlToFocus.container.removeClass('pwpcp-control-focused');
        }, 2000);
      };

      // be sure there is the control and update dynamic color message text
      if (controlToFocus) {
        if (linkEl) {
          linkEl.onclick = innerLink;
        } else {
          innerLink();
        }
      }
    },
    /**
     * Reset control -> setting value to the value according
     * to the given mode argument
     * @static
     * @param  {Control} control  The control whose setting has to be reset
     * @param  {string} resetType Either `'initial'` or `'factory'`
     * @return {boolean}          Whether the reset has succeded
     */
    resetControl: function (control, resetType) {
      var params = control.params;
      if (params.type === 'pwpcp_dummy') {
        return true;
      }
      var value;
      if (resetType === 'last' && !_.isUndefined(params.vLast)) {
        value = params.vLast;
      } else if (resetType === 'initial') {
        value = params.vInitial;
      } else if (resetType === 'factory') {
        value = params.vFactory;
      }
      if (value) {
        control.setting.set(value);
        return true;
      } else {
        return false;
      }
    },
    /**
     * Check if the given type of reset is needed for a specific control
     * @param  {Control} control  The control which need to be checked
     * @param  {string} resetType Either `'initial'` or `'factory'`
     * @return {boolean}          Whether the reset has succeded
     */
    _isResetNeeded: function (control, resetType) {
      var params = control.params;
      if (!control.pwpcp || params.type === 'pwpcp_dummy') {
        return false;
      }
      var _softenize = control.softenize;
      var value = _softenize(control.setting());
      if (resetType === 'last' && !_.isUndefined(params.vLast)) {
        return value !== _softenize(params.vLast);
      } else if (resetType === 'initial') {
        return value !== _softenize(params.vInitial);
      } else if (resetType === 'factory') {
        return value !== _softenize(params.vFactory);
      }
    },
    /**
     * Force `setting.set`.
     * Use case:
     * When a required text control content gets deleted by the user,
     * the extras dropdown shows the reset buttons enabled but clicking on any
     * of them doesn't give any effect in the UI. Why? Because when the input
     * field gets emptied the validate function set the setting to the last
     * value using `return this.setting()`, this returning value it is likely
     * to be the same as the initial session or the factory value, therefore
     * before and after the user has clicked the reset button the value of the
     * setting could stay the same. Despite this make sense, the input field
     * gets out of sync, it becomes empty, while the setting value remains the
     * latest valid value).
     * The callback that should be called on reset (the `syncUIfromAPI` method)
     * in this scenario doesn't get called because in the WordPress
     * `customize-base.js#187` there is a check that return the function if the
     * setting has been set with the same value as the last one, preventing so
     * to fire the callbacks binded to the setting and, with these, also our
     * `syncUIfromAPI` that would update the UI, that is our input field with
     * the resetted value. To overcome this problem we can force the setting to
     * set anyway by temporarily set the private property `_value` to a dummy
     * value and then re-setting the setting to the desired value, in this way
     * the callbacks are fired and the UI get back in sync.
     *
     * @param  {wp.customize.Setting} setting
     * @param  {string} value
     */
    _forceSettingSet: function (setting, value, dummyValue) {
      setting['_value'] = dummyValue || 'dummy'; // whitelisted from uglify \\
      setting.set(value);
    },
    /**
     * Is setting value (`control.setting()`) empty?
     * Used to check if required control's settings have instead an empty value
     * @see php class method `PWPcp_Sanitize::is_setting_value_empty()`
     * @param  {string}  value
     * @return {Boolean}
     */
    _isSettingValueEmpty: function (value) {
      // first try to compare it to an empty string
      if (value === '') {
        return true;
      } else {
        // if it's a jsonized value try to parse it
        try {
          value = JSON.parse(value);
        } catch(e) {}

        // and then see if we have an empty array or an empty object
        if ((_.isArray(value) || _.isObject(value)) && _.isEmpty(value)) {
          return true;
        }

        return false;
      }
    },
    /**
     * Each control execute callback with control as argument
     * @static
     * @param {function} callback
     */
    _eachControl: function (callback) {
      var wpApiControl = wpApi.control;
      for (var controlId in wpApi.settings.controls) {
        var control = wpApiControl(controlId);
        // @@doubt, probably unneeded check, @@hacky dummy exception \\
        if (control && control.params.type !== 'pwpcp_dummy') {
          callback(control);
        }
      }
    },
    /**
     * Is the control's setting using the `theme_mods` API?
     * @param  {string}  controlId The control id
     * @return {Boolean}
     */
    _isThemeModApi: function (controlId) {
      return !regexSettingApi.test(controlId);
    },
    /**
     * Is the control's setting using the `settings` API?
     * It is when the control id is structured as: `themeprefix[setting-id]`
     * @param  {string}  controlId The control id
     * @return {Boolean}
     */
    _isSettingApi: function (controlId) {
      return regexSettingApi.test(controlId);
    },
    /**
     * Selectize render option function
     *
     * @abstract
     * @param  {Object} item     The selectize option object representation.
     * @param  {function} escape Selectize escape function.
     * @return {string}          The option template.
     */
    _selectizeRenderSize: function (item, escape) {
      return '<div class="pwpcpsize-selectOption">' +
          '<i>' + escape(item.valueCSS) + '</i> ' + escape(item.label) +
        '</div>';
    },
    /**
     * Selectize render option function
     *
     * @abstract
     * @param  {Object} item     The selectize option object representation.
     * @param  {function} escape Selectize escape function.
     * @return {string}          The option template.
     */
    _selectizeRenderColor: function (item, escape) {
      return '<div class="pwpcpcolor-selectOption" style="border-color:' +
        escape(item.valueCSS) + '">' + escape(item.label) + '</div>';
    }
  };
})();

// export to public API
api.Utils = Utils;
