(function($) {
    $.fn.svgSpriteBrowserDialogAjaxCallback = function(fieldEditID, selectedSprite) {
      if ($("#" + fieldEditID).length) {
        // submitted selected id.
        $("#" + fieldEditID).val(selectedSprite).trigger('change');
      }
    };
  })(jQuery);