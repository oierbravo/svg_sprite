
(function($, Drupal) {
  Drupal.behaviors.entityReferenceTree = {
    attach: function(context, settings) {
      $(".js-svg-sprite-browser-wrapper", context)
      .once("svgSpriteBrowserBehavior")
      .each(function() {
        const fieldEditName = $("#svg-sprite-browser-widget-field-id").val();
        const widgetElement = $("[data-drupal-selector='" + fieldEditName + "']");
        // Avoid ajax callback from running following codes again.
        let refreshSelected = function (){
          let selectedId = $('#svg-sprite-browser-selected-sprite').val();
          $(".js-svg-sprite-browser-item").removeClass('checked');
          console.log(selectedId);
          $("[data-svg-sprite-id='" + selectedId +"']").addClass('checked');

        }
        refreshSelected();

        if (widgetElement.length) {
          console.log(fieldEditName);
          $('.js-svg-sprite-browser-item').click(function(ev){
            let id = $(this).data('svg-sprite-id');
            console.log('selected id click', id);
            $('#svg-sprite-browser-selected-sprite').val(id);
            refreshSelected();
          })


          // Search filter box.
          let to = false;
          $("#entity-reference-tree-search").keyup(function() {
            const searchInput = $(this);
            if (to) {
              clearTimeout(to);
            }
            to = setTimeout(function() {
              const v = searchInput.val();
              treeContainer.jstree(true).search(v);
            }, 250);
          });
        }
      });
    }
  };
})(jQuery, Drupal);


(function($) {
  $.fn.svgSpriteBrowserDialogAjaxCallback = function(fieldEditID, selectedSprite) {
    if ($("#" + fieldEditID).length) {
      // submitted selected id.
      $("#" + fieldEditID).val(selectedSprite).trigger('change');
    }
  };
})(jQuery);
