(function($) {
    $.fn.svgSpriteBrowserDialogAjaxCallback = function(fieldEditID, selectedSprite) {
      if ($(`input[data-drupal-selector="${fieldEditID}"]`).length) {
        // submitted selected id.
        $(`input[data-drupal-selector="${fieldEditID}"]`)
          .val(selectedSprite)
          .trigger("change");
      }
    };
  })(jQuery);