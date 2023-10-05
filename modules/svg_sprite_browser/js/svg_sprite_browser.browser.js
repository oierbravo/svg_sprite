
(function($, Drupal) {
  Drupal.behaviors.svgSpriteBrowser = {
    attach: function(context, settings) {
      var wrapperElement = once('svgSpriteBrowserBehavior', '.js-svg-sprite-browser-wrapper', context);
      wrapperElement
      .forEach(function() {
        const fieldEditName = $("#svg-sprite-browser-widget-field-id").val();
        const widgetElement = $("[data-drupal-selector='" + fieldEditName + "']");
        // Avoid ajax callback from running following codes again.
        let refreshSelected = function (){
          let selectedId = $('#svg-sprite-browser-selected-sprite').val();
          $(".js-svg-sprite-browser-item").removeClass("checked");
          $("[data-svg-sprite-id='" + selectedId +"']").addClass('checked');

        }
        refreshSelected();
        if (widgetElement.length) {
          $('.js-svg-sprite-browser-item').click(function(ev){
            let id = $(this).data('svg-sprite-id');
            $('#svg-sprite-browser-selected-sprite').val(id);
            refreshSelected();
          })


          // Search filter box.
          let to = false;
          $("#svg-sprite-browser-search").change(function() {
            const searchInput = $(this);
            if (to) {
              clearTimeout(to);
            }
            to = setTimeout(function() {
              const searchValue = searchInput.val();
              $('.js-svg-sprite-browser-item').each(function(){
                let element =  $(this);
                let id = element.data('svg-sprite-id');

                if(id.search(searchValue) === -1) {
                  element.fadeOut(100);
                } else {
                  element.fadeIn(100);

                }
              });
            }, 250);
          });
        }
      });
    }
  };
})(jQuery, Drupal);
