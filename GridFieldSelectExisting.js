
(function($){

  $(function(){

    $.entwine('ss', function($) {

      $('.ss-gridfield input.checkbox.select-existing').entwine({

        onmatch: function() {
          this.closest('tr').addClass('select-existing');
        }

      });

      $('.ss-gridfield tr.select-existing').entwine({

        onmatch: function() {
          var checkbox = this.find('input.checkbox');
          this.set(checkbox.prop('checked'));
        },

        onclick: function() {
          this.toggle();
        },

        set: function(set) {
          var checkbox = this.find('input.checkbox');
          checkbox.prop('checked', set);
          if (set) {
            this.addClass('select-existing-selected');
          } else {
            this.removeClass('select-existing-selected');
          }
        },

        toggle: function() {
          var checkbox = this.find('input.checkbox');
          this.set(!checkbox.prop('checked'));
        },

      });


    });

  });

})(jQuery);
