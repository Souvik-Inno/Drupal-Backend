(function ($, Drupal) {
  Drupal.behaviors.myBootstrapBehavior = {
    attach: function(context, settings) {
      $('#phone', context).on('input', function() {
        var input = $(this).val();
        if (input.length === 10) {
          var formatted = '(' + input.substr(0, 3) + ') ' + input.substr(3, 3) + '-' + input.substr(6);
          $(this).val(formatted);
        }
      });
    }
  };
})(jQuery, Drupal);
