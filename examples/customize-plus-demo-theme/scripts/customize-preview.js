(function (window, $, wp, api) {
  'use strict';

  var wpApi = wp.customize;
  var _toCSS = api['toCSS'];

  var $body = $('body');

  $.fn.flash = function(duration) {
    this.addClass('flash');
    setTimeout(function () {
      this.removeClass('flash');
    }.bind(this), 300);
    return this;
  };

  var settingsToColor = [
    'color-native',
    'color',
    'color-no-transparent',
    'color-alpha',
    'color-palette1',
    'color-palette2',
    'color-palette3'
  ];
  _.each(settingsToColor, function (setting) {
    wpApi(setting, function (value) {
      value.bind(function (to) {
        $('#' + setting).css('background', to).flash();
      });
    });
  });

  var settingsToWidth = [
    'slider',
    'slider-em',
    'slider-px-percent',
  ];
  _.each(settingsToWidth, function (setting) {
    wpApi(setting, function (value) {
      value.bind(function (to) {
        $('#' + setting).css('width', to).flash();
      });
    });
  });

  var settingsToText = [
    'radio',
    'buttonset',
    'buttonset-three',
    'buttonset-four',
    'radio-image',
    'radio-image-custom',
    'text',
    'text-max-length',
    'text-required',
    'text-url',
    'text-email',
    'textarea',
    'number',
    'number-min',
    'number-max',
    'number-step',
    'toggle',
    'multicheck',
    'multicheck-sortable',
    'select',
    // 'select-multiple',
    'select-selectize',
    'select-selectize-options',
    'select-selectize-more-items',
    'select-selectize-tags-plugins',
    'tags',
    'tags-removable',
    'tags-sortable-removable',
    'tags-max-items',
    'sortable',
    // 'sortable-editable'
  ].concat(settingsToColor, settingsToWidth);

  _.each(settingsToText, function (setting) {
    wpApi(setting, function (value) {
      value.bind(function (to) {
        $('#' + setting).text(to).flash();
      });
    });
  });

  var settingsToHtml = [
    'textarea-html',
    'textarea-wp_editor',
    'textarea-wp_editor-options',
  ];
  _.each(settingsToHtml, function (setting) {
    wpApi(setting, function (value) {
      value.bind(function (to) {
        $('#' + setting).html(to).flash();
      });
    });
  });


})(window, jQuery, wp, PWPcp);